$(document).ready(function () {
  $(document).on("click", ".btnDeleteEvent", function (e) {
    e.preventDefault();
    const eventId = $(this).data("id");
    // alert("Event ID: " + eventId);

    Swal.fire({
      title: "Yakin ingin menghapus event ini?",
      text: "Data event akan dinonaktifkan dan tidak bisa diakses lagi.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#6c757d",
      confirmButtonText: "Ya, hapus!",
      cancelButtonText: "Batal",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: BASE_URL + "admin/events/" + eventId + "/delete",
          type: "POST",
          success: function (res) {
            if (res.ok) {
              Swal.fire("Berhasil!", "Event telah dihapus.", "success").then(
                () => {
                  location.reload();
                }
              );
            } else {
              Swal.fire(
                "Gagal!",
                res.error || "Event tidak dapat dihapus.",
                "error"
              );
            }
          },
          error: function (xhr) {
            let msg = "Terjadi kesalahan pada server.";
            try {
              const res = JSON.parse(xhr.responseText);
              if (res.error) msg = res.error;
            } catch (e) {}
            Swal.fire("Error!", msg, "error");
          },
        });
      }
    });
  });
});
