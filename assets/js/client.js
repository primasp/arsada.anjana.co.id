function scrollToSection(sectionId) {
  const targetElement = document.getElementById(sectionId);
  if (targetElement) {
    targetElement.scrollIntoView({
      behavior: "smooth", // Efek scrolling yang halus
      block: "start", // Posisi elemen di bagian atas layar
    });
  }
}

// Event listener untuk mengontrol perpindahan antar bagian
// document.addEventListener("DOMContentLoaded", function () {
//   // Misalkan saat klik tombol submit di "Cari Property", kita pindah ke available-properties
//   document.getElementById("btnSubmit").addEventListener("click", function (e) {
//     e.preventDefault(); // Mencegah submit default
//     // Simulasikan pemrosesan pencarian properti, lalu scroll ke available-properties
//     setTimeout(() => {
//       document.getElementById("available-properties").style.display = "block";
//       scrollToSection("available-properties");
//     }, 500); // Simulasi loading
//   });

//   // Saat available-rooms ditampilkan, scroll ke sana
//   document
//     .getElementById("available-rooms")
//     .addEventListener("click", function () {
//       scrollToSection("available-rooms");
//     });

//   // Saat reserve-rooms ditampilkan, scroll ke sana
//   document
//     .getElementById("reserve-rooms")
//     .addEventListener("click", function () {
//       scrollToSection("reserve-rooms");
//     });
// });

$(document).ready(function () {
  console.log("Client JS loaded");

  // $("#btnSubmit").on("click", function (e) {
  //   e.preventDefault(); // Mencegah submit default
  //   // Simulasikan pemrosesan pencarian properti, lalu scroll ke available-properties
  //   setTimeout(() => {
  //     $("#available-properties").css("display", "block");
  //     scrollToSection("available-properties");
  //   }, 500); // Simulasi loading
  // });

  // // Saat available-rooms ditampilkan, scroll ke sana
  // $("#available-rooms").on("click", function () {
  //   scrollToSection("available-rooms");
  // });

  // // Saat reserve-rooms ditampilkan, scroll ke sana
  // $("#reserve-rooms").on("click", function () {
  //   scrollToSection("reserve-rooms");
  // });

  var today = new Date().toISOString().split("T")[0];
  $("#start_date").attr("min", today);

  $(".select2").select2({
    allowClear: true, // Tambahkan tombol untuk menghapus pilihan
    width: "100%", // Sesuaikan lebar dengan elemen parent
  });

  $(".numeric").on("input", function () {
    this.value = this.value.replace(/[^0-9]/g, "");
  });

  $(document).on("click", "#btnProsesReserve", function (e) {
    e.preventDefault();
    // alert("121");

    var fullName = $("#full_name").val();
    var nikKtp = $("#nik_ktp").val();
    var email = $("#email").val();
    var phoneNumber = $("#phone_number").val();
    var proofOfPayment = $("#proof_of_payment")[0].files[0]; // File Bukti Pembayaran
    var uploadKtp = $("#upload_ktp")[0].files[0]; // File KTP
    var room_id = $("#room_id").val();
    var property_id = $("#property_id").val();
    var quantity = $("#quantity").val();
    var address = $("#address").val();
    var rent_period = $("#rent_period").val();
    var start_date = $("#start_date").val();
    var end_date = $("#end_date").val();
    var total_rent = $("#total_rent").val();
    var payment_method = $("#payment_method").val();

    var isBringingPartner = $("#is_bringing_partner").val();
    var marriageProof = $("#marriage_proof")[0]?.files[0];
    var numberOfPeople = $("#number_of_people").val();

    var emptyFields = [];

    // Periksa input satu per satu
    if (!fullName) emptyFields.push("Nama Lengkap");
    if (!nikKtp) emptyFields.push("NIK KTP");
    if (!email) emptyFields.push("Email");
    if (!phoneNumber) emptyFields.push("Nomor Telepon");
    if (!proofOfPayment) emptyFields.push("Bukti Pembayaran");
    if (!uploadKtp) emptyFields.push("KTP");
    if (!room_id) emptyFields.push("Room ID");
    if (!property_id) emptyFields.push("Property ID");
    if (!quantity) emptyFields.push("Jumlah");
    if (!address) emptyFields.push("Alamat");
    if (!rent_period) emptyFields.push("Periode Sewa");
    if (!start_date) emptyFields.push("Tanggal Mulai");
    if (!end_date) emptyFields.push("Tanggal Selesai");
    if (!total_rent) emptyFields.push("Total Sewa");
    if (!payment_method) emptyFields.push("Metode Pembayaran");
    if (!numberOfPeople) emptyFields.push("Jumlah Orang");

    if (isBringingPartner === "yes" && !marriageProof) {
      emptyFields.push("Bukti Pernikahan");
    }

    // Validasi data sebelum submit
    if (emptyFields.length > 0) {
      // alert("Harap isi semua field dan unggah file yang diperlukan.");

      Swal.fire({
        title: "Data Belum Lengkap!",
        html: `<b>Harap isi kolom berikut:</b><br> <ul>${emptyFields
          .map((field) => `<li>${field}</li>`)
          .join("")}</ul>`,
        icon: "error",
        confirmButtonText: "OKE",
      });
      return;
    }

    // Buat FormData untuk mengirim file dan input
    var formData = new FormData();
    formData.append("full_name", fullName);
    formData.append("nik_ktp", nikKtp);
    formData.append("email", email);
    formData.append("phone_number", phoneNumber);
    formData.append("proof_of_payment", proofOfPayment);
    formData.append("upload_ktp", uploadKtp);
    formData.append("room_id", room_id);
    formData.append("property_id", property_id);
    formData.append("quantity", quantity);
    formData.append("address", address);
    formData.append("rent_period", rent_period);
    formData.append("start_date", start_date);
    formData.append("end_date", end_date);
    formData.append("total_rent", total_rent);
    formData.append("payment_method", payment_method);
    formData.append("is_bringing_partner", isBringingPartner);
    formData.append("marriage_proof", marriageProof);
    formData.append("number_of_people", numberOfPeople);

    console.log(formData);

    $.ajax({
      url: BASE_URL + "ClientController/save_rental_transaction",
      type: "POST",
      data: formData,
      contentType: false, // Penting untuk FormData
      processData: false, // Penting untuk FormData
      dataType: "json",
      success: function (response) {
        console.log(response);
        if (response.status === "success") {
          Swal.fire({
            title: "Sukses!",
            text: response.message,
            icon: "success",
            confirmButtonText: "OK",
          }).then(() => {
            // window.location.href = "<?= base_url('ClientController'); ?>";
            window.location.href = BASE_URL + "Client";
          });
        } else {
          Swal.fire({
            title: "Error!",
            text: response.message,
            icon: "error",
            confirmButtonText: "OK",
          });
        }
      },
      error: function (xhr, status, error) {
        Swal.fire({
          title: "Error!",
          text: "Terjadi kesalahan saat mengirim data.",
          icon: "error",
          confirmButtonText: "OK",
        });
        console.error("AJAX Error:", status, error);
      },
    });
  });

  $("#rent_period, #quantity, #start_date").on("change", function () {
    var rentPeriod = $("#rent_period").val();
    var quantity = $("#quantity").val();
    var startDate = $("#start_date").val();

    // Cek apakah rent_period, quantity, dan start_date sudah terisi
    if (rentPeriod && quantity && startDate) {
      updateEndDate();
    } else {
      // Kosongkan field end_date jika input belum lengkap
      $("#end_date").val("");
    }

    function updateEndDate() {
      var rentPeriod = $("#rent_period").val();
      var quantity = parseInt($("#quantity").val());
      var startDate = $("#start_date").val();

      // Pengecekan tambahan (untuk berjaga-jaga)
      if (!rentPeriod || !quantity || !startDate) {
        return; // Jika salah satu belum diisi, hentikan eksekusi
      }

      var startDateObj = new Date(startDate);
      var endDateObj = new Date(startDate); // Copy date object

      // Kalkulasi berdasarkan periode
      if (rentPeriod === "harian") {
        endDateObj.setDate(startDateObj.getDate() + quantity); // Tambahkan jumlah hari
      } else if (rentPeriod === "mingguan") {
        endDateObj.setDate(startDateObj.getDate() + quantity * 7); // Tambahkan jumlah minggu (7 hari per minggu)
      } else if (rentPeriod === "bulanan") {
        endDateObj.setMonth(startDateObj.getMonth() + quantity); // Tambahkan jumlah bulan
      }

      // Format tanggal akhir sewa ke dd.mm.yyyy
      var formattedEndDate = formatDateToIndo(endDateObj);
      $("#end_date").val(formattedEndDate); // Set tanggal akhir di input form
    }
  });

  $(document).on("click", ".property-item", function (event) {
    var propertyId = $(this).data("property-id");
    var propertyName = $(this).data("property-name");
    var startDate = $(this).data("start-date");
    var endDate = $(this).data("end-date");
    var rentPeriod = $(this).data("rent-period");
    var quantity = $(this).data("quantity");

    // alert(propertyId);
    // alert(rentPeriod);
    // alert(quantity);
    // alert(startDate);
    // alert(endDate);

    $.ajax({
      url: BASE_URL + "ClientController/available_rooms",
      type: "POST",
      data: {
        property_id: propertyId,
        start_date: startDate,
        end_date: endDate,
        rent_period: rentPeriod,
        quantity: quantity,
        propertyName: propertyName,
      },
      success: function (response) {
        $("#available-properties").hide();
        $("#available-rooms").html(response).show();
        scrollToSection("available-rooms");
      },
      error: function (xhr, status, error) {
        // Tampilkan kesalahan di console jika tidak sukses
        console.log("Error status: " + status);
        console.log("Error details: " + error);
        console.log("Response text: " + xhr.responseText);

        alert("Terjadi kesalahan saat mengambil kamar yang tersedia.");
      },
    });
  });

  $(document).on("click", ".reserve-item", function (event) {
    event.preventDefault();

    var roomId = $(this).data("room-id");
    var startDate = $(this).data("start-date");
    var endDate = $(this).data("end-date");
    var rentPeriod = $(this).data("rent-period");
    var quantity = $(this).data("quantity");

    $.ajax({
      url: BASE_URL + "ClientController/reserve_room",
      type: "POST",
      data: {
        room_id: roomId,
        start_date: startDate,
        end_date: endDate,
        rent_period: rentPeriod,
        quantity: quantity,
      },
      success: function (response) {
        $("#available-rooms").hide();
        $("#reserve-rooms").html(response).show();

        scrollToSection("reserve-rooms");
      },
    });
  });

  $("#cariPropertyForm").submit(function (event) {
    event.preventDefault();

    var rentPeriod = $("#rent_period").val();
    var quantity = $("#quantity").val();
    var startDate = $("#start_date").val();
    var endDate = $("#end_date").val();

    var startDateObj = new Date(startDate);
    var startDate = formatDateToIndo(startDateObj);

    // Referensi tombol submit
    var btnSubmit = $("#btnSubmit");
    var spinner = $("#spinner");
    var btnText = $("#btnText");

    // Aktifkan spinner dan ubah teks tombol
    btnSubmit.prop("disabled", true); // Disable tombol untuk mencegah pengiriman ulang
    spinner.removeClass("d-none"); // Tampilkan spinner
    btnText.text("Loading..."); // Ubah teks tombol menjadi loading

    $.ajax({
      url: BASE_URL + "ClientController/available_properties",
      type: "POST",
      data: {
        rent_period: rentPeriod,
        quantity: quantity,
        start_date: startDate,
        end_date: endDate,
      },
      beforeSend: function () {
        // Tampilkan loader sebelum request dimulai
        $("#loader").removeClass("d-none");
      },
      success: function (response) {
        // Sembunyikan loader
        $("#loader").addClass("d-none");

        btnSubmit.prop("disabled", false);
        spinner.addClass("d-none"); // Sembunyikan spinner
        btnText.text("Cari Property Tersedia"); // Kembalikan teks tombol

        $("#available-properties").hide();
        $("#available-rooms").hide();
        $("#reserve-rooms").hide();
        $("#available-properties").html(response).show();

        // Tambahkan scroll ke available-properties
        scrollToSection("available-properties");
      },
      error: function (xhr, status, error) {
        // Sembunyikan loader jika terjadi error
        $("#loader").addClass("d-none");

        btnSubmit.prop("disabled", false);
        spinner.addClass("d-none"); // Sembunyikan spinner
        btnText.text("Cari Property Tersedia"); // Kembalikan teks tombol

        // Tampilkan kesalahan di console jika tidak sukses
        console.log("Error status: " + status);
        console.log("Error details: " + error);
        console.log("Response text: " + xhr.responseText);

        // Tampilkan alert untuk user jika terjadi kesalahan
        alert("Terjadi kesalahan saat mengambil daftar properti.");
      },
    });
  });

  function formatDateToIndo(date) {
    var day = ("0" + date.getDate()).slice(-2); // Ambil hari, tambahkan 0 jika satu digit
    var month = ("0" + (date.getMonth() + 1)).slice(-2); // Ambil bulan, +1 karena bulan di JavaScript dimulai dari 0
    var year = date.getFullYear(); // Ambil tahun
    return day + "." + month + "." + year; // Format dd.mm.yyyy
  }
});

function validateFile(input) {
  const file = input.files[0];
  const allowedTypes = ["image/jpeg", "image/png", "application/pdf"];
  const maxSize = 5 * 1024 * 1024; // 2 MB

  if (file) {
    if (!allowedTypes.includes(file.type)) {
      Swal.fire({
        title: "Error!",
        text: "Format file tidak didukung. Gunakan JPG, PNG, atau PDF.",
        icon: "error",
        confirmButtonText: "OK",
      });
      input.value = ""; // Hapus input file
      return;
    }

    if (file.size > maxSize) {
      Swal.fire({
        title: "Error!",
        text: "Ukuran file terlalu besar. Maksimal 2 MB.",
        icon: "error",
        confirmButtonText: "OK",
      });
      input.value = ""; // Hapus input file
      return;
    }
  }
}

function loadFile(event, labelId) {
  const input = event.target; // Input file element
  const file = input.files[0]; // File yang dipilih
  const allowedTypes = ["image/jpeg", "image/jpg", "image/png"];
  const maxSize = 5 * 1024 * 1024; // 2 MB

  if (file) {
    // Validasi tipe file
    if (!allowedTypes.includes(file.type)) {
      alert("Hanya file dengan format JPEG, JPG, atau PNG yang diperbolehkan.");
      input.value = ""; // Reset input file
      // document.getElementById(labelId).innerText = "Choose File";
      return;
    }

    // Validasi ukuran file
    if (file.size > maxSize) {
      alert("Ukuran file tidak boleh lebih dari 2 MB.");
      input.value = ""; // Reset input file
      // document.getElementById(labelId).innerText = "Choose File";
      return;
    }
    // Tampilkan nama file di label
  }
}
function toggleMarriageProof() {
  const isBringingPartner = document.getElementById(
    "is_bringing_partner"
  ).value;
  const marriageProofSection = document.getElementById(
    "marriage_proof_section"
  );
  if (isBringingPartner === "yes") {
    marriageProofSection.style.display = "block";
  } else {
    marriageProofSection.style.display = "none";
  }
}
