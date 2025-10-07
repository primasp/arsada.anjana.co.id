$(document).ready(function () {
  console.log("Form Builder JS Loaded");

  // const BASE_URL = "<?= site_url() ?>";
  const formId = $("#form_id").val();

  const secModal = new bootstrap.Modal(document.getElementById("modalSection"));
  const qModal = new bootstrap.Modal(document.getElementById("modalQuestion"));
  const oModal = new bootstrap.Modal(document.getElementById("modalOption"));

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
    if (!confirm("Hapus section ini?")) return;

    $.ajax({
      url: BASE_URL + "admin/forms/section/" + sectionId + "/delete",
      type: "POST",
      success: function (res) {
        if (res.ok) {
          Swal.fire("Berhasil", "Section dihapus.", "success").then(() => {
            location.reload();
          });
        } else {
          Swal.fire("Gagal", "Tidak dapat menghapus section.", "error");
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
      is_required: $("#qRequired").is(":checked") ? 1 : 0,
    };

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
          Swal.fire("Gagal", "Tidak dapat menambahkan opsi.", "error");
        }
      },
      error: function () {
        Swal.fire("Error", "Terjadi kesalahan saat menambah opsi.", "error");
      },
    });
  });
});
