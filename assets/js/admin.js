$(document).ready(function () {
  console.log("Admin JS loaded");
  $("#owner_id").on("change", function () {
    var ownerId = $(this).val();
    if (ownerId) {
      $.ajax({
        url: BASE_URL + "Master_C/getPropertiesByOwner/" + ownerId,
        method: "GET",
        dataType: "json",
        success: function (properties) {
          $("#property_id").empty();
          $("#property_id").append('<option value="">Pilih Properti</option>');
          // Isi dropdown Properti dengan data dari server
          $.each(properties, function (index, property) {
            $("#property_id").append(
              '<option value="' +
                property.property_id +
                '">' +
                property.property_name +
                "</option>"
            );
          });
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.error("Error: " + textStatus + " " + errorThrown);
        },
      });
    }
    // alert(ownerId);
  });

  $("#filterFormRoom").on("submit", function (e) {
    e.preventDefault();

    // Ambil data dari form
    let formData = $(this).serialize();
    $.ajax({
      url: BASE_URL + "Master_C/filter_ms_room",
      type: "POST",
      data: formData,
      dataType: "json",
      success: function (response) {
        let tableBody = "";
        if (response.length > 0) {
          $.each(response, function (index, item) {
            let statusDropdown = "";

            if (item.status === "00" || item.status === "01") {
              statusDropdown = `
                  <div class="dropdown action-label">
                      <a class="custom-badge ${
                        item.status === "00" ? "status-yellow" : "status-green"
                      } dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                          ${
                            item.status === "00"
                              ? "Dalam perbaikan"
                              : "Tersedia"
                          }
                      </a>
                      <div class="dropdown-menu dropdown-menu-end status-staff">
                          <a class="dropdown-item" href="javascript:;" data-status="00">Dalam perbaikan</a>
                          <a class="dropdown-item" href="javascript:;" data-status="01">Tersedia</a>
                      </div>
                  </div>`;
            } else if (item.status === "02") {
              // Jika status adalah 02, arahkan ke URL tertentu
              statusDropdown = `
                  <div class="dropdown action-label">
                      <a class="custom-badge status-blue" href="${BASE_URL}Transaksi-All">
                          Butuh Konfirmasi
                      </a>
                  </div>`;
            } else {
              // Jika status bukan 00 atau 01, hanya tampilkan status tanpa dropdown
              statusDropdown = `
               <div class="dropdown action-label">
                   <a class="custom-badge ${
                     item.status === "02" ? "status-blue" : "status-red"
                   }">
                       ${item.status === "02" ? "Butuh Konfirmasi" : "Terisi"}
                   </a>
               </div>`;
            }

            tableBody += `
                <tr data-id="${item.room_id}">
                    <td>
                        <div class="form-check check-tables">
                            <input class="form-check-input" type="checkbox" value="something">
                        </div>
                    </td>
                    <td>${item.owner_name}</td>
                    <td>${item.property_name}</td>
                    <td>${item.room_number}</td>
                    <td>${item.room_type}</td>
                    <td>
                        Hari : <a class="btn btn-sm btn-primary edit-price" data-type="daily" href="javascript:;"> ${formatRupiah(
                          item.daily_price
                        )}</a>
                        &nbsp;&nbsp;| Minggu : <a class="btn btn-sm btn-primary edit-price" data-type="weekly" href="javascript:;">${formatRupiah(
                          item.weekly_price
                        )}</a>
                        &nbsp;&nbsp;| Bulan : <a class="btn btn-sm btn-primary edit-price" data-type="monthly" href="javascript:;">${formatRupiah(
                          item.monthly_price
                        )}</a>
                    </td>
                    <td>${statusDropdown}</td>
                    <td class="text-end">
                        <div class="dropdown dropdown-action">
                            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="#"><i class="fa-solid fa-pen-to-square m-r-5"></i> Edit</a>
                                <a class="dropdown-item" href="#"><i class="fa fa-trash-alt m-r-5"></i> Delete</a>
                            </div>
                        </div>
                    </td>
                </tr>`;
          });
        } else {
          tableBody =
            '<tr><td colspan="8" class="text-center">No data found</td></tr>';
        }

        // Update tabel
        $(".table tbody").html(tableBody);

        // Tambahkan event listener untuk mengubah status
        $(".dropdown-item").on("click", function () {
          const newStatus = $(this).data("status");
          const row = $(this).closest("tr");
          $.ajax({
            url: BASE_URL + "Master_C/update_room_status",
            type: "POST",
            data: {
              room_id: row.data("id"), // Pastikan ID kamar tersedia di atribut data-id
              status: newStatus,
            },
            success: function () {
              alert("Status updated successfully!");
              $("#filterFormRoom").submit(); // Reload data tabel
            },
            error: function () {
              alert("Error updating status. Please try again.");
            },
          });
        });
      },
      error: function () {
        alert("Error fetching data. Please try again.");
      },
    });

    console.log(formData);
  });

  $(document).on("click", ".edit-price", function () {
    const priceType = $(this).data("type"); // Ambil tipe harga (daily, weekly, monthly)
    const row = $(this).closest("tr"); // Dapatkan baris tabel terkait
    const roomId = row.data("id"); // Ambil room_id dari data-id
    const currentPrice = $(this)
      .text()
      .replace(/[^0-9]/g, "");

    // Tampilkan SweetAlert dengan input form
    Swal.fire({
      title: `Edit ${
        priceType.charAt(0).toUpperCase() + priceType.slice(1)
      } Price`,
      input: "number",
      inputLabel: "Enter new price",
      inputValue: currentPrice,
      showCancelButton: true,
      confirmButtonText: "Save",
      cancelButtonText: "Cancel",
      inputValidator: (value) => {
        if (!value || value <= 0) {
          return "Please enter a valid price!";
        }
      },
    }).then((result) => {
      if (result.isConfirmed) {
        const newPrice = result.value;

        // Kirim data ke server untuk update harga
        $.ajax({
          url: BASE_URL + "Master_C/update_room_price",
          type: "POST",
          data: {
            room_id: roomId,
            price_type: priceType,
            price: newPrice,
          },
          success: function () {
            Swal.fire("Success", "Price updated successfully!", "success");
            $("#filterFormRoom").submit(); // Reload data tabel
          },
          error: function () {
            Swal.fire(
              "Error",
              "Failed to update price. Please try again.",
              "error"
            );
          },
        });
      }
    });
    // alert(currentPrice);
  });

  function formatRupiah(number) {
    return new Intl.NumberFormat("id-ID", {
      style: "currency",
      currency: "IDR",
      minimumFractionDigits: 0,
    })
      .format(number)
      .replace("IDR", "")
      .trim();
  }
});
