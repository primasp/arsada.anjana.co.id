$(document).ready(function () {
  console.log("Transaksi JS loaded");

  // const mainWrapper = document.querySelector(".main-wrapper");
  // if (mainWrapper && mainWrapper.hasAttribute("aria-hidden")) {
  //   mainWrapper.removeAttribute("aria-hidden");
  // }

  // Pastikan juga saat tombol ubah harga ditekan, aria-hidden dimatikan lagi
  // document
  //   .getElementById("btnEditPrice")
  //   .addEventListener("click", function () {
  //     alert("33");
  //     if (mainWrapper && mainWrapper.hasAttribute("aria-hidden")) {
  //       mainWrapper.removeAttribute("aria-hidden");
  //     }
  //   });

  $(".select2").select2({
    allowClear: true, // Tambahkan tombol untuk menghapus pilihan
    width: "100%", // Sesuaikan lebar dengan elemen parent
  });
  let dt;
  $(function () {
    // Ambil instance jika sudah ter-init oleh theme, kalau belum baru init
    if ($.fn.DataTable.isDataTable("#all-transaksi-table")) {
      dt = $("#all-transaksi-table").DataTable();
      dt.order([[1, "desc"]]).draw();
    } else {
      dt = $("#all-transaksi-table").DataTable({
        retrieve: true, // aman bila ternyata sudah ter-init
        responsive: true,
        autoWidth: false,
        order: [[1, "desc"]],
        columnDefs: [
          { orderable: false, targets: [0, 9] },
          { className: "text-center align-middle", targets: "_all" },
        ],
        pageLength: 10,
        lengthMenu: [
          [10, 25, 50, 100, -1],
          [10, 25, 50, 100, "Semua"],
        ],
        dom: "lfrtip",
      });
    }
  });

  var today = new Date().toISOString().split("T")[0];
  // $("#start_date").attr("min", today);

  // ==== Sync & Recalc ====
  let __syncingDP = false;

  function syncDP(from) {
    const total = parseRupiahToNumber($("#total_price").val());
    let p = parseFloat($("#dp_percent").val());
    let a = parseRupiahToNumber($("#dp_amount").val());

    if (from === "percent") {
      if (isNaN(p)) p = 0;
      // clamp 0..100
      p = Math.max(0, Math.min(100, p));
      const calc = Math.round((p / 100) * total);
      $("#dp_percent").val(p); // tampilkan clamp
      $("#dp_amount").val(calc); // isi nominal dari persen
    } else if (from === "amount") {
      if (isNaN(a)) a = 0;
      // clamp 0..total
      a = Math.max(0, Math.min(total, a));
      const calc = total > 0 ? (a / total) * 100 : 0;
      $("#dp_amount").val(a); // tampilkan clamp nominal
      $("#dp_percent").val(calc.toFixed(2)); // isi persen dari nominal
    } else {
      // total berubah: prioritaskan persen jika ada, else nominal
      if ($("#dp_percent").val()) {
        syncDP("percent");
        return;
      } else {
        syncDP("amount");
        return;
      }
    }
  }

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
      // alert(formattedEndDate);
      loadProperties();
    }

    function formatDateToIndo(date) {
      var day = ("0" + date.getDate()).slice(-2); // Ambil hari, tambahkan 0 jika satu digit
      var month = ("0" + (date.getMonth() + 1)).slice(-2); // Ambil bulan, +1 karena bulan di JavaScript dimulai dari 0
      var year = date.getFullYear(); // Ambil tahun
      return day + "." + month + "." + year; // Format dd.mm.yyyy
    }
  });

  $(document).on(
    "input change",
    "#total_price, #dp_amount, #dp_percent",
    recalcRemaining
  );

  $(document).on("change", "input[name='payment_mode']", function () {
    // alert("ok");
    const mode = $("input[name='payment_mode']:checked").val();
    // alert(mode);
    if (mode === "DP") {
      $("#dp_section").removeClass("d-none");
    } else {
      $("#dp_section").addClass("d-none");
      $("#dp_amount").val("");
      $("#dp_percent").val("");
      $("#dp_due_date").val("");
      $("#dp_proof_of_payment").val("");
      $("#remaining_amount").val("");
    }
  });

  function parseRupiahToNumber(str) {
    if (!str) return 0;
    return (
      parseFloat(
        String(str).replace(/\s+/g, "").replace(/[Rp.]/g, "").replace(",", ".")
      ) || 0
    );
  }

  function formatID(x) {
    return (x ?? 0).toLocaleString("id-ID");
  }

  function recalcRemaining() {
    const total = parseRupiahToNumber($("#total_price").val());
    const dpNominal = parseRupiahToNumber($("#dp_amount").val());
    const remaining = Math.max(total - dpNominal, 0);
    $("#remaining_amount").val(formatID(remaining));
  }

  // ==== Bindings dua arah tanpa loop ====
  $(document).on("input change", "#dp_percent", function () {
    if (__syncingDP) return;
    __syncingDP = true;
    syncDP("percent");
    recalcRemaining();
    __syncingDP = false;
  });

  $(document).on("input change", "#dp_amount", function () {
    if (__syncingDP) return;
    __syncingDP = true;
    syncDP("amount");
    recalcRemaining();
    __syncingDP = false;
  });

  $(document).on("input change", "#total_price", function () {
    if (__syncingDP) return;
    __syncingDP = true;
    syncDP("total"); // hitung ulang saat total berubah
    recalcRemaining();
    __syncingDP = false;
  });

  function loadProperties() {
    const rentPeriod = $("#rent_period").val();
    const quantity = $("#quantity").val();
    const startDate = $("#start_date").val();
    const endDate = $("#end_date").val();

    if (rentPeriod && quantity && startDate && endDate) {
      $.ajax({
        url: BASE_URL + "Transaksi_C/available_properties",
        type: "POST",
        data: {
          rent_period: rentPeriod,
          quantity: quantity,
          start_date: startDate,
          end_date: endDate,
        },
        success: function (response) {
          let data = JSON.parse(response);
          console.log(data);

          let options = '<option value="">-- Pilih Property --</option>';

          if (data.length > 0) {
            data.forEach(function (item) {
              options += `<option value="${item.property_id}">${item.property_name}</option>`; // Tambahkan dokter ke dropdown
            });
          } else {
            options = '<option value="">Tidak ada Property tersedia</option>'; // Jika tidak ada dokter
          }

          $("#property").html(options); // Perbarui dropdown dokter
          $("#jam_slot").html('<option value="">-- Pilih Jam Slot --</option>');
        },
        error: function (xhr) {
          alert(
            xhr.responseJSON.message || "Terjadi kesalahan. Silakan coba lagi."
          );
        },
      });
    }
  }

  $("#start_date,#rent_period,#quantity").on("change", function () {
    // resetDropdowns();
    $("#property").html('<option value="">-- Pilih Property --</option>');
    $("#kamar").html('<option value="">-- Pilih Property --</option>');
    loadProperties();
  });

  $("#rent_period, #quantity, #kamar").on("change", calculateTotalPrice);
  function calculateTotalPrice() {
    let rentPeriod = $("#rent_period").val();
    let quantity = parseInt($("#quantity").val());
    let roomId = $("#kamar").val();
    let propertyId = $("#property").val();
    let startDate = $("#start_date").val();
    let endDate = $("#end_date").val();

    // console.log("rentPeriod :" + rentPeriod);
    // console.log("quantity :" + quantity);
    // console.log("startDate :" + startDate);
    // console.log("endDate :" + endDate);
    // console.log("property :" + property);
    // console.log("kamar :" + kamar);

    if (
      rentPeriod &&
      quantity &&
      roomId &&
      startDate &&
      endDate &&
      propertyId
    ) {
      $.ajax({
        url: BASE_URL + "Transaksi_C/getTotalHarga",
        type: "POST",
        data: {
          room_id: roomId,
          rent_period: rentPeriod,
          quantity: quantity,
        },
        dataType: "json",
        success: function (response) {
          if (response.status === "success") {
            const totalHarga = response.price;

            // alert(
            //   new Intl.NumberFormat("id-ID", {
            //     style: "currency",
            //     currency: "IDR",
            //   }).format(totalHarga)
            // );

            $("#total_price").val(
              new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR",
              }).format(totalHarga)
            );
          } else {
            $("#total_price").val("Gagal menghitung harga");
          }
        },
        error: function () {
          // Jika terjadi kesalahan pada AJAX, kosongkan total harga
          $("#total_price").val("Terjadi kesalahan saat menghitung harga");
        },
      });
    } else {
      $("#total_price").val("");
    }
  }

  $("#property").on("change", function () {
    let propertyId = $("#property").val();
    let rentPeriod = $("#rent_period").val();
    let quantity = $("#quantity").val();
    let startDate = $("#start_date").val();
    let endDate = $("#end_date").val();

    if (propertyId && rentPeriod && quantity && startDate && endDate) {
      $.ajax({
        url: BASE_URL + "Transaksi_C/available_rooms",
        type: "POST",
        data: {
          property_Id: propertyId,
          rent_period: rentPeriod,
          quantity: quantity,
          start_date: startDate,
          end_date: endDate,
        },
        success: function (response) {
          let data = JSON.parse(response);
          console.log(data);

          let options = '<option value="">-- Pilih Kamar --</option>';

          if (data.length > 0) {
            data.forEach(function (item) {
              options += `<option value="${item.room_id}">${item.room_number}</option>`; // Tambahkan dokter ke dropdown
            });
          } else {
            options = '<option value="">Tidak ada kamar tersedia</option>'; // Jika tidak ada dokter
          }

          $("#kamar").html(options); // Perbarui dropdown dokter
        },
        error: function (xhr) {
          alert(
            xhr.responseJSON.message || "Terjadi kesalahan. Silakan coba lagi."
          );
        },
      });
    }
  });

  $(document).on("click", ".btnEditPrice", function () {
    // document
    //   .getElementById("btnEditPrice")
    //   .addEventListener("click", function () {
    let room_id = $("#kamar").val();
    let rent_period = $("#rent_period").val();
    let quantity = $("#quantity").val();
    let isUpdtHrg = true;
    // alert(isUpdtHrg);
    Swal.fire({
      title: "Ubah Total Harga",
      // input: "number",
      input: "text", // ganti jadi text supaya bisa format
      inputLabel: "Masukkan harga baru",
      inputPlaceholder: "Contoh: 500000",
      showCancelButton: true,
      confirmButtonText: "Simpan",
      cancelButtonText: "Batal",
      didOpen: () => {
        const input = Swal.getInput();
        input.addEventListener("input", function (e) {
          // Ambil hanya digit
          let value = e.target.value.replace(/\D/g, "");
          // Format ribuan pakai titik
          e.target.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        });
      },
      preConfirm: (value) => {
        const rawNumber = parseInt(value.replace(/\./g, ""), 10);
        if (isNaN(rawNumber) || rawNumber <= 0) {
          Swal.showValidationMessage("Harga tidak boleh kosong atau 0");
        }
        return rawNumber;
      },
    }).then((result) => {
      // if (result.isConfirmed) return;
      if (!result.isConfirmed) return;

      // console.log("123" + result.value);
      const newPrice = result.value;
      document.getElementById("total_price").value = newPrice;
      const fd = new FormData();
      fd.append("room_id", room_id || "");
      fd.append("rent_period", rent_period || "");
      fd.append("quantity", quantity || "");
      fd.append("isUpdtHrg", (isUpdtHrg = true));
      // opsional: kirim harga baru jika server ingin override langsung
      fd.append("total_price", newPrice);

      // alert("123");
      // console.log(fd);

      // AJAX update harga ke server
      fetch(BASE_URL + "Transaksi_C/getTotalHarga", {
        // fetch("update_harga_endpoint", {
        method: "POST",
        headers: { "X-Requested-With": "XMLHttpRequest" },
        body: fd,
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.status === "success") {
            Swal.fire("Berhasil!", "Harga berhasil diperbarui.", "success");

            // Jika server balas price hasil perhitungan/override, pakai itu untuk tampilan

            const finalPrice =
              typeof data.price !== "undefined" ? data.price : newPrice;

            // alert(finalPrice);

            // alert(
            //   new Intl.NumberFormat("id-ID", {
            //     style: "currency",
            //     currency: "IDR",
            //   }).format(finalPrice)
            // );

            $("#total_price").val(
              new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR",
              }).format(finalPrice)
            );

            // update tampilan terformat (Rp ...)
            // const idrFormat = new Intl.NumberFormat("id-ID", {
            //   style: "currency",
            //   currency: "IDR",
            // }).format(finalPrice);

            // $("#total_price_display").val(idrFormat); // untuk tampilan
            // $("#total_price").val(finalPrice);

            // const display = document.getElementById("total_price_display");
            // if (display) display.value = idr.format(Number(finalPrice));

            // simpan kembali nilai mentah
            // document.getElementById("total_price").value = Number(finalPrice);

            Swal.fire("Berhasil!", "Harga berhasil diperbarui.", "success");
          } else {
            Swal.fire("Gagal!", "Tidak dapat memperbarui harga.", "error");
          }
        })
        .catch((err) => {
          Swal.fire(
            "Error!",
            "Terjadi kesalahan saat menghubungi server.",
            "error"
          );
        });
    });
  });

  $(document).on("click", "#btnProsesReserve", function (e) {
    e.preventDefault();
    var fullName = $("#full_name").val();
    var nikKtp = $("#nik_ktp").val();
    var email = $("#email").val();
    var phoneNumber = $("#phone_number").val();
    var proofOfPayment = $("#proof_of_payment")[0].files[0]; // File Bukti Pembayaran
    var uploadKtp = $("#upload_ktp")[0].files[0]; // File KTP
    var room_id = $("#kamar").val();
    var property_id = $("#property").val();
    var quantity = $("#quantity").val();
    var address = $("#address").val();
    var rent_period = $("#rent_period").val();
    var start_date = $("#start_date").val();
    var end_date = $("#end_date").val();
    var total_rent = $("#total_price").val();
    var autoRenew = $("#auto_renew").is(":checked") ? 1 : 0; // ✅ tambahan ini

    // ... (ambil semua variabel lama)
    const paymentMode = $("input[name='payment_mode']:checked").val(); // 'FULL' | 'DP'
    const dpAmountRaw = $("#dp_amount").val();
    const dpPercent = $("#dp_percent").val();
    const dpDueDate = $("#dp_due_date").val();
    const dpProof = $("#dp_proof_of_payment")[0]?.files?.[0] || null;
    // var payment_method = $("#payment_method").val();

    const errors = [];
    if (paymentMode === "DP") {
      if (!dpAmountRaw && !dpPercent) errors.push("DP (Nominal) atau DP (%)");
      if (!dpDueDate) errors.push("Jatuh Tempo DP");
    }

    // ... (validasi lama)
    if (errors.length) {
      Swal.fire({
        title: "Data Belum Lengkap!",
        html: `<b>Harap isi kolom berikut:</b><br><ul>${errors
          .map((x) => `<li>${x}</li>`)
          .join("")}</ul>`,
        icon: "error",
        confirmButtonText: "OKE",
      });
      return;
    }

    var emptyFields = [];

    if (!fullName) emptyFields.push("Nama Lengkap");
    // if (!nikKtp) emptyFields.push("NIK KTP");
    // if (!email) emptyFields.push("Email");
    // if (!phoneNumber) emptyFields.push("Nomor Telepon");
    // if (!proofOfPayment) emptyFields.push("Bukti Pembayaran");
    // if (!uploadKtp) emptyFields.push("KTP");
    if (!room_id) emptyFields.push("Room ID");
    if (!property_id) emptyFields.push("Property ID");
    if (!quantity) emptyFields.push("Jumlah");
    // if (!address) emptyFields.push("Alamat");
    if (!rent_period) emptyFields.push("Periode Sewa");
    if (!start_date) emptyFields.push("Tanggal Mulai");
    if (!end_date) emptyFields.push("Tanggal Selesai");
    if (!total_rent) emptyFields.push("Total Sewa");
    // if (!payment_method) emptyFields.push("Metode Pembayaran");

    if (emptyFields.length > 0) {
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
    formData.append("auto_renew", autoRenew); // ✅ kirim status ceklis
    // formData.append("payment_method", payment_method);

    formData.append("payment_mode", paymentMode);
    formData.append("dp_amount", dpAmountRaw); // bisa rupiah or angka mentah; server akan bersihkan
    formData.append("dp_percent", dpPercent || "");
    formData.append("dp_due_date", dpDueDate || "");
    if (dpProof) formData.append("dp_proof_of_payment", dpProof);

    console.log(formData);

    $.ajax({
      url: BASE_URL + "Transaksi_C/save_rental_transaction",
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
            window.location.href = BASE_URL + "Transaksi-Add";
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

  $("#approve-selected").on("click", function () {
    // alert("123");
    let selectedTransactions = [];
    $(".select-checkbox:checked").each(function () {
      selectedTransactions.push({
        transaction_id: $(this).data("transaction-id"),
        room_id: $(this).data("room-id"),
      });
    });

    if (selectedTransactions.length === 0) {
      alert("Silakan pilih transaksi untuk di-approve.");
      return;
    }

    Swal.fire({
      title: "Konfirmasi Approve",
      text: "Apakah Anda yakin ingin menyetujui transaksi yang dipilih?",
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Ya, Setujui",
      cancelButtonText: "Batal",
    }).then((result) => {
      if (result.isConfirmed) {
        // Kirim data ke server melalui AJAX
        $.ajax({
          url: BASE_URL + "Transaksi_C/approve_selected",
          type: "POST",
          data: { transactions: selectedTransactions },
          dataType: "json",
          success: function (response) {
            if (response.status === "success") {
              Swal.fire(
                "Berhasil",
                "Transaksi berhasil di-approve.",
                "success"
              ).then(() => {
                location.reload(); // Muat ulang halaman
              });
            } else {
              Swal.fire(
                "Gagal",
                "Gagal meng-approve transaksi. Silakan coba lagi.",
                "error"
              );
            }
          },
          error: function () {
            Swal.fire(
              "Kesalahan",
              "Terjadi kesalahan. Silakan coba lagi.",
              "error"
            );
          },
        });
      }
    });

    // $.ajax({
    //   url: BASE_URL + "Transaksi_C/approve_selected",
    //   type: "POST",
    //   data: { transactions: selectedTransactions },
    //   dataType: "json",
    //   success: function (response) {
    //     if (response.status === "success") {
    //       alert("Transaksi berhasil di-approve.");
    //       location.reload(); // Muat ulang halaman
    //     } else {
    //       alert("Gagal meng-approve transaksi. Silakan coba lagi.");
    //     }
    //   },
    //   error: function () {
    //     alert("Terjadi kesalahan. Silakan coba lagi.");
    //   },
    // });

    // alert(selectedTransactions);
  });

  $("#filterFormTransaksi").on("submit", function (e) {
    e.preventDefault();

    const formData = $(this).serialize();

    $.ajax({
      url: BASE_URL + "Transaksi_C/filter_transaksi",
      type: "POST",
      data: formData,
      dataType: "json",
      success: function (response) {
        // map response ke array-of-rows untuk DataTables
        const rows = (response || []).map(function (item) {
          // status button
          let statusButton = "";
          switch (item.status_sewa) {
            case "Upcoming":
              statusButton = `<a href="${BASE_URL}Transaksi-Upcoming" class="btn btn-sm btn-primary"><i class="fa fa-clock"></i> Akan Datang</a>`;
              break;
            case "Berakhir Hari Ini":
              statusButton = `<a href="${BASE_URL}Transaksi-BerakhirHariIni" class="btn btn-sm btn-danger"><i class="fa fa-calendar-day"></i> Berakhir Hari Ini</a>`;
              break;
            case "Ending Soon (<2 Days)":
              statusButton = `<a href="${BASE_URL}Transaksi-EndingSoon" class="btn btn-sm btn-warning"><i class="fa fa-hourglass-half"></i> Akan Berakhir &lt; 2 Hari</a>`;
              break;
            case "Ongoing":
              statusButton = `<a href="${BASE_URL}Transaksi-Ongoing" class="btn btn-sm btn-success"><i class="fa fa-play-circle"></i> Sedang Disewakan</a>`;
              break;
            case "Lease Ended":
              statusButton = `<a href="${BASE_URL}Transaksi-LeaseEnded" class="btn btn-sm btn-danger"><i class="fa fa-times-circle"></i> Masa Sewa Berakhir</a>`;
              break;
            case "Di Stop":
              statusButton = `<span class="btn btn-sm btn-dark"><i class="fa fa-stop-circle"></i> Di Stop</span>`;
              break;
            default:
              statusButton = `<span class="btn btn-sm btn-secondary"><i class="fa fa-question-circle"></i> Status Tidak Diketahui</span>`;
          }

          // tanggal & hari
          const opt = { day: "2-digit", month: "long", year: "numeric" };
          const startDate = new Date(item.start_date);
          const endDate = new Date(item.end_date);
          const formattedStart = startDate.toLocaleDateString("id-ID", opt);
          const formattedEnd = endDate.toLocaleDateString("id-ID", opt);
          // const totalDays = Math.round(
          //   (endDate - startDate) / (1000 * 60 * 60 * 24)
          // );

          const qty = parseInt(item.quantity || "1", 10);
          let totalDays;
          switch ((item.rent_period || "").toLowerCase()) {
            case "bulanan":
              totalDays = 30 * qty; // selalu 30 hari per bulan (sesuai kebutuhanmu)
              break;
            case "mingguan":
              totalDays = 7 * qty; // 7 hari per minggu
              break;
            default: // 'harian'
              totalDays = 1 * qty; // 1 hari per quantity
          }

          // auto renew icon
          const isAuto =
            item.auto_renew === true ||
            item.auto_renew === 1 ||
            item.auto_renew === "1" ||
            item.auto_renew === "true" ||
            item.auto_renew === "t";
          const autoIcon = isAuto
            ? `<span title="Perpanjangan Otomatis"><i class="fas fa-sync-alt text-success ms-1"></i></span>`
            : "";

          // verif bayar
          let verifBtn = "";
          if (item.verif_bayar === "pending") {
            verifBtn = `<a href="${BASE_URL}Transaksi-Pending" class="btn btn-sm btn-warning"><i class="fa fa-exclamation-circle"></i> Pending</a>`;
          } else if (item.verif_bayar === "approve") {
            verifBtn = `<span class="btn btn-sm btn-success"><i class="fa fa-check-circle"></i> Approve</span>`;
          }

          // tombol aksi
          const editBtn = `
          <button class="btn btn-sm btn-info edit-trx"
            data-id="${item.transaction_id}"
            data-rent_period="${item.rent_period}"
            data-start_date="${item.start_date}"
            data-end_date="${item.end_date}"
            data-quantity="${item.quantity || 1}"
            data-auto_renew="${isAuto ? 1 : 0}"
            data-verif_bayar="${item.verif_bayar}">
            <i class="fa fa-edit"></i> Edit
          </button>`;

          let stopBtn = "";
          if (item.done_status !== "02") {
            const roomId = item.room_id || item.roomId || "";
            stopBtn = `
            <button class="btn btn-sm btn-danger stop-trx"
              data-id="${item.transaction_id}"
              data-room="${roomId}"
              data-prop-id="${item.property_id}">
              <i class="fa fa-stop-circle"></i> Stop
            </button>`;
          }

          // Kembalikan 1 row (array 10 kolom mengikuti thead)
          return [
            // item.transaction_id,
            `<div class="text-center">${item.transaction_id}</div>`,
            item.property_name,
            // item.room_number,
            `<div class="text-center">${item.room_number}</div>`,

            // item.full_name,
            `<div class="text-center">${item.full_name}</div>`,

            `<div class="text-center">
              ${item.rent_period.toUpperCase()} : <b>${totalDays} Hari</b> ${autoIcon}
              <br>${formattedStart} - ${formattedEnd}
          </div>`,

            `<div class="text-center">
              Rp ${Number(item.total_sewa || 0).toLocaleString("id-ID")}
            </div>`,

            // Status Pembayaran → center
            `<div class="text-center">${verifBtn}</div>`,

            // Status Sewa → center
            `<div class="text-center">${statusButton}</div>`,

            // Aksi → center (opsional)
            `<div class="text-center">${editBtn} ${stopBtn}</div>`,
          ];
        });

        // update DataTable TANPA reload halaman & mempertahankan state sort/search
        dt.clear().rows.add(rows).draw(false);
      },
      error: function () {
        alert("Error fetching data. Please try again.");
      },
    });
  });

  $(document).on("click", ".edit-trx", function () {
    const id = $(this).data("id");
    // alert(id);
    $("#edit_transaction_id").val(id);
    $("#edit_rent_period").val($(this).data("rent_period"));
    $("#edit_quantity").val($(this).data("quantity") || 1);
    $("#edit_auto_renew").prop("checked", $(this).data("auto_renew") == "t");
    $("#edit_verif_bayar").val($(this).data("verif_bayar"));

    const start = $(this).data("start_date");
    $("#edit_start_date").val(start);

    // hitung end awal
    const end = computeEndDate(
      $("#edit_rent_period").val(),
      parseInt($("#edit_quantity").val() || "1", 10),
      start
    );

    $("#edit_end_date").val(end);

    new bootstrap.Modal(document.getElementById("editTrxModal")).show();
  });

  // submit edit
  $("#editTrxForm").on("submit", function (e) {
    e.preventDefault();
    const payload = {
      transaction_id: $("#edit_transaction_id").val(),
      rent_period: $("#edit_rent_period").val(),
      quantity: parseInt($("#edit_quantity").val() || "1", 10),
      start_date: $("#edit_start_date").val(),
      end_date: $("#edit_end_date").val(),
      // auto_renew: $("#edit_auto_renew").is(":checked") ? true : false,
      auto_renew: $("#edit_auto_renew").is(":checked") ? 1 : 0,
      verif_bayar: $("#edit_verif_bayar").val(),
    };

    $.ajax({
      url: BASE_URL + "Transaksi_C/update_transaction",
      type: "POST",
      data: payload,
      dataType: "json",
      success: function (res) {
        if (res.status === "success") {
          // Swal.fire("Tersimpan", res.message, "success").then(() =>
          //   location.reload()
          // );

          Swal.fire("Tersimpan", res.message, "success").then(() => {
            // Tutup modal sebelum reload
            var modalEl = document.getElementById("editTrxModal");
            var modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) {
              modalInstance.hide();
            }
            location.reload();
          });
        } else {
          Swal.fire("Gagal", res.message || "Gagal menyimpan.", "error");
        }
      },
      error: function () {
        Swal.fire("Error", "Terjadi kesalahan saat menyimpan.", "error");
      },
    });

    console.log(payload);
  });

  // auto recalc saat user ubah start/rent_period/qty
  $("#edit_rent_period, #edit_quantity, #edit_start_date").on(
    "change keyup",
    function () {
      const end = computeEndDate(
        $("#edit_rent_period").val(),
        parseInt($("#edit_quantity").val() || "1", 10),
        $("#edit_start_date").val()
      );
      $("#edit_end_date").val(end);
    }
  );

  $(document).on("click", ".stop-trx", function () {
    const id = $(this).data("id");
    const room = $(this).data("room");

    Swal.fire({
      title: "Hentikan Transaksi?",
      text: "Transaksi akan ditutup (done_status=01) dan auto_renew dimatikan.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Ya, Hentikan",
      cancelButtonText: "Batal",
    }).then((r) => {
      if (!r.isConfirmed) return;

      $.ajax({
        url: BASE_URL + "Transaksi_C/stop_transaction",
        type: "POST",
        data: { transaction_id: id, room_id: room },
        dataType: "json",
        success: function (res) {
          if (res.status === "success") {
            Swal.fire("Berhasil", res.message, "success").then(() =>
              location.reload()
            );
          } else {
            Swal.fire("Gagal", res.message || "Gagal menghentikan.", "error");
          }
        },
        error: function () {
          Swal.fire("Error", "Terjadi kesalahan.", "error");
        },
      });
    });
  });

  $(document).on("click", ".reject-row", function () {
    let transactionId = $(this).data("transaction-id");
    let roomId = $(this).data("room-id");

    Swal.fire({
      title: "Konfirmasi Penolakan",
      text: "Apakah Anda yakin ingin menolak transaksi ini?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Ya, Tolak",
      cancelButtonText: "Batal",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: BASE_URL + "Transaksi_C/reject_selected",
          type: "POST",
          data: {
            transactions: [{ transaction_id: transactionId, room_id: roomId }],
          },
          dataType: "json",
          success: function (response) {
            if (response.status === "success") {
              Swal.fire(
                "Berhasil",
                "Transaksi berhasil ditolak.",
                "success"
              ).then(() => location.reload());
            } else {
              Swal.fire("Gagal", "Gagal menolak transaksi.", "error");
            }
          },
          error: function () {
            Swal.fire("Kesalahan", "Terjadi kesalahan.", "error");
          },
        });
      }
    });
  });

  $(document).on("click", ".approve-row", function () {
    // alert("124");
    let transactionId = $(this).data("transaction-id");
    let roomId = $(this).data("room-id");

    const stage = $(this).data("stage") || "full"; // 'dp' | 'full'

    const title =
      stage === "dp" ? "Konfirmasi Approve DP" : "Konfirmasi Approve Pelunasan";
    const text =
      stage === "dp"
        ? "Menyetujui pembayaran DP?"
        : "Menyetujui pelunasan (lunas)?";

    // Tampilkan konfirmasi dengan SweetAlert2
    Swal.fire({
      title,
      text,
      icon: "question",
      // title: "Konfirmasi Approve",
      // text: "Apakah Anda yakin ingin menyetujui transaksi ini?",
      // icon: "question",
      showCancelButton: true,
      confirmButtonText: "Ya, Setujui",
      cancelButtonText: "Batal",
    }).then((result) => {
      if (result.isConfirmed) {
        // Kirim data ke server melalui AJAX
        $.ajax({
          url: BASE_URL + "Transaksi_C/approve_selected",
          type: "POST",
          data: {
            transactions: [
              { transaction_id: transactionId, room_id: roomId, stage },
            ],
          },
          dataType: "json",
          success: function (response) {
            if (response.status === "success") {
              Swal.fire(
                "Berhasil",
                "Transaksi berhasil di-approve.",
                "success"
              ).then(() => {
                location.reload(); // Muat ulang halaman
              });
            } else {
              // Swal.fire(
              //   "Gagal",
              //   "Gagal meng-approve transaksi. Silakan coba lagi.",
              //   "error"
              // );

              Swal.fire(
                "Gagal",
                response.message || "Gagal meng-approve transaksi.",
                "error"
              );
            }
          },
          error: function () {
            Swal.fire(
              "Kesalahan",
              "Terjadi kesalahan. Silakan coba lagi.",
              "error"
            );
          },
        });
      }
    });
  });
});

// helper hitung end_date
function computeEndDate(rentPeriod, qty, startDateStr) {
  if (!startDateStr || !qty) return "";
  const start = new Date(startDateStr + "T00:00:00");
  let end = new Date(start);

  if (rentPeriod === "mingguan") {
    // qty x 7 hari (tanpa -1 hari karena model "checkout" di end_date)
    end.setDate(end.getDate() + qty * 7);
  } else if (rentPeriod === "harian") {
    end.setDate(end.getDate() + qty);
  } else {
    // bulanan
    // tambah qty bulan (checkout di end_date)
    const d = new Date(start);
    d.setMonth(d.getMonth() + qty);
    end = d;
  }

  const yyyy = end.getFullYear();
  const mm = String(end.getMonth() + 1).padStart(2, "0");
  const dd = String(end.getDate()).padStart(2, "0");
  return `${yyyy}-${mm}-${dd}`;
}

function loadFile(event, labelId) {
  const input = event.target; // Input file element
  const file = input.files[0]; // File yang dipilih
  const allowedTypes = ["image/jpeg", "image/jpg", "image/png"];
  const maxSize = 2 * 1024 * 1024; // 2 MB

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
