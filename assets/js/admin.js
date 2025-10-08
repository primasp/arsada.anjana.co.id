$(document).ready(function () {
  console.log("Form Builder JS Loaded");

  // const BASE_URL = "<?= site_url() ?>";
  const formId = $("#form_id").val();

  const secModal = new bootstrap.Modal(document.getElementById("modalSection"));
  const qModal = new bootstrap.Modal(document.getElementById("modalQuestion"));
  const oModal = new bootstrap.Modal(document.getElementById("modalOption"));
  const sectionList = document.getElementById("sectionList");

  if (sectionList) {
    Sortable.create(document.getElementById("sectionList"), {
      animation: 150,
      handle: ".drag-handle",
      onEnd: function () {
        const items = [];
        $("#sectionList .section-item").each(function (index) {
          items.push({ id: $(this).data("id"), sort_order: index + 1 });
        });
        updateSort("section", items);
      },
    });

    // Sortable.create(sectionList, {
    //   animation: 150,
    //   handle: ".section-item",
    //   onEnd: function () {
    //     const items = [];
    //     $("#sectionList .section-item").each(function (index) {
    //       items.push({ id: $(this).data("id"), sort_order: index + 1 });
    //     });
    //     updateSort("section", items);
    //   },
    // });
  }

  // === QUESTION SORT ===
  // $(".question-list").each(function () {
  //   alert("ss");
  //   Sortable.create(this, {
  //     animation: 150,
  //     handle: ".question-item",
  //     onEnd: () => {
  //       const items = [];
  //       $(".question-list .question-item").each(function (index) {
  //         items.push({ id: $(this).data("id"), sort_order: index + 1 });
  //       });
  //       updateSort("question", items);
  //     },
  //   });
  // });

  $(".question-list").each(function () {
    Sortable.create(this, {
      animation: 150,
      handle: ".drag-handle",
      onEnd: function () {
        const items = [];
        $(".question-list .question-item").each(function (index) {
          items.push({ id: $(this).data("id"), sort_order: index + 1 });
        });
        updateSort("question", items);
      },
    });
  });

  function updateSort(type, items) {
    $.ajax({
      url: BASE_URL + "admin/forms/sort-items",
      type: "POST",
      data: { type, items },
      success: function (res) {
        if (res.ok) {
          console.log("✅ " + type + " updated:", res.message);
        } else {
          Swal.fire(
            "Gagal",
            res.error || "Tidak bisa menyimpan urutan.",
            "error"
          );
        }
      },
      error: function () {
        Swal.fire("Error", "Terjadi kesalahan pada server.", "error");
      },
    });
  }

  // === ADD SECTION ===
  $("#btnAddSection").on("click", () => {
    secModal.show();
  });

  $("#saveSection").on("click", () => {
    // alert(formId);
    const data = {
      form_id: formId,
      title: $("#sectionTitle").val(),
      description: $("#sectionDesc").val(),
      sort_order: $("#sectionSort").val() || 1,
    };

    $.ajax({
      url: BASE_URL + "admin/forms/section/store",
      type: "POST",
      data: data,
      success: function (res) {
        if (res.ok) {
          Swal.fire(
            "Berhasil",
            "Section berhasil ditambahkan!",
            "success"
          ).then(() => {
            location.reload();
          });
        } else {
          Swal.fire("Gagal", "Tidak dapat menambahkan section.", "error");
        }
      },
      error: function () {
        Swal.fire("Error", "Terjadi kesalahan saat menambah section.", "error");
      },
    });
  });

  // === DELETE SECTION ===
  $(document).on("click", ".btnDelSection", function () {
    const sectionId = $(this).data("id");
    // if (!confirm("Hapus section ini?")) return;

    Swal.fire({
      title: "Hapus Section?",
      text: "Seluruh pertanyaan dalam section ini juga akan dinonaktifkan. Lanjutkan?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#6c757d",
      confirmButtonText: "Ya, hapus!",
      cancelButtonText: "Batal",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: BASE_URL + "admin/forms/section/" + sectionId + "/delete",
          type: "POST",
          success: function (res) {
            if (res.ok) {
              Swal.fire(
                "Berhasil",
                "Section dan seluruh pertanyaan di dalamnya dinonaktifkan.",
                "success"
              ).then(() => {
                location.reload();
              });
            } else {
              // Swal.fire("Gagal", "Tidak dapat menghapus section.", "error");
              Swal.fire(
                "Gagal",
                res.error || "Tidak dapat menghapus section.",
                "error"
              );
            }
          },
          error: function () {
            Swal.fire(
              "Error",
              "Terjadi kesalahan saat menghapus section.",
              "error"
            );
          },
        });
      }
    });
  });

  $(document).on("click", ".btnDeleteEvent", function (e) {
    alert("delete");
  });

  // === ADD QUESTION ===
  $(".btnAddQuestion").on("click", function () {
    $("#qSectionId").val($(this).data("section"));
    qModal.show();
  });

  $("#saveQuestion").on("click", function () {
    const data = {
      form_id: formId,
      section_id: $("#qSectionId").val(),
      question_type: $("#qType").val(),
      label: $("#qLabel").val(),
      placeholder: $("#qPlaceholder").val(),
      // is_required: $("#qRequired").is(":checked") ? 1 : 0,
      is_required: $("#qRequired").val() === "true",
    };
    console.log("121" + data);

    $.ajax({
      url: BASE_URL + "admin/forms/question/store",
      type: "POST",
      data: data,
      success: function (res) {
        if (res.ok) {
          Swal.fire(
            "Berhasil",
            "Pertanyaan berhasil ditambahkan!",
            "success"
          ).then(() => {
            location.reload();
          });
        } else {
          Swal.fire("Gagal", "Tidak dapat menambahkan pertanyaan.", "error");
        }
      },
      error: function () {
        Swal.fire(
          "Error",
          "Terjadi kesalahan saat menambah pertanyaan.",
          "error"
        );
      },
    });
  });

  // === DELETE QUESTION ===
  $(document).on("click", ".btnDelQuestion", function () {
    const id = $(this).data("id");
    if (!confirm("Hapus pertanyaan ini?")) return;

    $.ajax({
      url: BASE_URL + "admin/forms/question/" + id + "/delete",
      type: "POST",
      success: function (res) {
        if (res.ok) {
          Swal.fire("Berhasil", "Pertanyaan dihapus.", "success").then(() => {
            location.reload();
          });
        } else {
          Swal.fire("Gagal", "Tidak dapat menghapus pertanyaan.", "error");
        }
      },
      error: function () {
        Swal.fire(
          "Error",
          "Terjadi kesalahan saat menghapus pertanyaan.",
          "error"
        );
      },
    });
  });

  // === ADD OPTION ===
  $(document).on("click", ".btnAddOption", function () {
    $("#optQid").val($(this).data("qid"));
    oModal.show();
  });

  $("#saveOption").on("click", function () {
    const data = {
      question_id: $("#optQid").val(),
      option_label: $("#optLabel").val(),
      option_value: $("#optValue").val(),
    };

    $.ajax({
      url: BASE_URL + "admin/forms/option/store",
      type: "POST",
      data: data,
      success: function (res) {
        if (res.ok) {
          Swal.fire(
            "Berhasil",
            "Opsi pertanyaan berhasil ditambahkan!",
            "success"
          ).then(() => {
            location.reload();
          });
        } else {
          // Swal.fire("Gagal", "Tidak dapat menambahkan opsi.", "error");
          Swal.fire(
            "Gagal",
            res.error || "Tidak dapat menambahkan opsi.",
            "error"
          );
        }
      },
      error: function (xhr) {
        // ✅ Tangani error AJAX seperti 500 atau timeout
        let msg = "Terjadi kesalahan saat menambah opsi.";
        try {
          const res = JSON.parse(xhr.responseText);
          if (res.error) msg = res.error;
        } catch (e) {}
        Swal.fire("Error", msg, "error");
      },
    });
  });
});
