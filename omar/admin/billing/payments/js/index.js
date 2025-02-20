$(document).ready(function () {
  let _month = document.querySelector("#monthsButtons button.active").dataset
    .month;
  let _parcialPaymentTotal = 0;
  function formatMoney(amount) {
    return new Intl.NumberFormat("en", {
      style: "currency",
      currency: "USD",
    }).format(amount);
  }
  init();
  $("#addChargeForm").submit(function (e) {
    e.preventDefault();
    const form = $(this)[0];
    const fd = new FormData(form);
    const data = {
      code: fd.get("code"),
      codeDescription: fd.get("codeDescription"),
      chargeTo: fd.get("chargeTo"),
      amount: fd.get("amount"),
      month: fd.get("month"),
    };
    if ($("#allMonths").prop("checked")) {
      data.allMonths = true;
    }
    $.ajax({
      type: "POST",
      url: form.action,
      data,
      dataType: "json",
      success: function (response) {
        console.log({ response });
        if (response.error) {
          $("#addChargeModal").modal("hide");
          Alert.fire({
            icon: "error",
            title: "Error!",
            text: response.message,
          });
        } else {
          const { rows } = response;
          rows.forEach((row) => {
            addChargeToTable(row.month, {
              id: row.codigo,
              description: row.desc1,
              date: row.fecha_d,
              debt: row.deuda,
              grade: row.grado,
              mt: row.mt,
            });
          });
          $("#addChargeModal").modal("hide");
          Toast.fire("Cargo añadido!", "", "success");
        }
      },
    });
  });
  $("#paymentReceiptForm").submit(function (e) {
    e.preventDefault();
    const form = $(this)[0];
    const fd = new FormData(form);
    const type = fd.get("type");
    const transaction = fd.get("transaction");
    const email = fd.get("email");
    const newEmail = fd.get("newEmail");
    const searchParams = new URLSearchParams({
      type,
      transaction,
    });
    if (email) {
      searchParams.append("email", email);
    }
    if (newEmail) {
      searchParams.append("newEmail", newEmail);
    }
    window.open("./pdf/receipt.php?" + searchParams.toString(), "receipt");
  });

  $("#statementForm").submit(function (e) {
    e.preventDefault();
    const accountId = $("#accountId").val();
    const form = $(this)[0];
    const fd = new FormData(form);
    const type = fd.get("type");
    const email = fd.get("email");
    const newEmail = fd.get("newEmail");

    const searchParams = new URLSearchParams({
      type,
      accountId,
    });
    if (email) {
      searchParams.append("email", email);
    }
    if (type === "3") {
      searchParams.append("month", _month);
    }
    if (newEmail) {
      searchParams.append("newEmail", newEmail);
    }
    window.open("./pdf/statement.php?" + searchParams.toString(), "statement");
  });
  $("#expiredModal").on("show.bs.modal", async function (event) {
    console.log("promise modal");
    const accountId = $("#accountId").val();
    $.ajax({
      type: "GET",
      url: "./includes/expired.php",
      data: { accountId, month: _month },
      // dataType: "json",
      success: function (charges) {
        console.log({ charges, month: _month });
        let total = 0;
        $("#expiredTable tbody").html("");
        charges.forEach(({ code, description, debt }) => {
          if (debt > 0) {
            total += debt;
            $("#expiredTable tbody").append(`
              <tr>
                <td>${code} ${description}</td>
                <th class="text-right">${formatMoney(debt)}</th>
              </tr>`);
          }
        });
        $("#expiredTotal").text(formatMoney(total));
      },
    });
  });
  $("#paymentPromiseDelete").click(function (e) {
    ConfirmationAlert.fire({
      title:
        __LANG === "es"
          ? "Esta seguro que desea borrar esta promesa de pago?"
          : "Are you sure you want to delete this payment promise?",
    }).then(function (result) {
      if (result.isConfirmed) {
        const accountId = $("#accountId").val();
        $.ajax({
          type: "POST",
          url: "./includes/paymentPromise.php",
          data: { accountId, deletePromise: true },
          dataType: "json",
          complete: function (response) {
            $("#paymentPromiseButton span").text("No");
            $("#paymentPromiseModal").modal("hide");
            Toast.fire("Promesa de pago eliminada!", "", "success");
          },
        });
      }
    });
  });
  $("#paymentPromiseModal").on("show.bs.modal", async function (event) {
    const accountId = $("#accountId").val();
    $.ajax({
      type: "GET",
      url: "./includes/paymentPromise.php",
      data: { accountId },
      dataType: "json",
      success: function (response) {
        $("#paymentPromiseDate").val(response.date);
        $("#paymentPromiseExpirationDate").val(response.expirationDate);
        $("#paymentPromiseDescription").val(response.description);
        $("#paymentPromiseAmount").val(response.amount);
        $("#paymentPromiseTime").val(response.time);
        $("#paymentPromiseNewAmount").val(response.newAmount);
        $("#paymentPromiseTotal").val(response.total);
      },
    });
  });
  $("#paymentPromiseForm").submit(function (e) {
    e.preventDefault();
    const form = $(this)[0];
    const accountId = $("#accountId").val();
    const fd = new FormData(form);
    const date = fd.get("date");
    const expirationDate = fd.get("expirationDate");
    const description = fd.get("description");
    const amount = fd.get("amount");
    const time = fd.get("time");
    const newAmount = fd.get("newAmount");
    const total = fd.get("total");

    $.ajax({
      type: "POST",
      url: "./includes/paymentPromise.php",
      data: {
        accountId,
        date,
        expirationDate,
        description,
        amount,
        time,
        newAmount,
        total,
      },
      dataType: "json",
      complete: function (response) {
        Toast.fire("Promesa de pago guardada", "", "success");
        $("#paymentPromiseButton span").text("Si");
        $("#paymentPromiseModal").modal("hide");
      },
    });
  });

  $("#latePaymentForm").submit(function (e) {
    e.preventDefault();
    const form = $(this)[0];
    const accountId = $("#accountId").val();
    const fd = new FormData(form);
    const observationType = fd.get("observationType");
    const alert = fd.get("alert");
    const info = fd.get("info");

    $.ajax({
      type: "POST",
      url: "./includes/latePayment.php",
      data: { accountId, observationType, alert, info },
      dataType: "json",
      complete: function (response) {
        Toast.fire("Pago moroso guardado", "", "success");
        $("#latePaymentModal").modal("hide");
      },
    });
    if (observationType || alert) {
      $("#latePaymentButton")
        .addClass("btn-danger")
        .removeClass("btn-secondary");
    } else {
      $("#latePaymentButton")
        .removeClass("btn-danger")
        .addClass("btn-secondary");
    }
  });
  $("#latePaymentModal").on("show.bs.modal", async function (event) {
    const { observationType, alert, info } = await getLatePaymentInfo();
    $("#latePaymentObservationType").val(observationType);
    $("#latePaymentAlert").prop("checked", alert);
    $("#latePaymentAditionalInfo").val(info);
  });

  $(".depositBtn").click(function () {
    const id = $(this).data("id");
    $.ajax({
      type: "GET",
      url: "./includes/deposit.php",
      data: { id },
      dataType: "json",
      success: function (response) {
        if (response.error) {
          Toast.fire("Hubo un error");
        } else {
          $("#minDeposit").val(response.minDeposit.toFixed(2));
          $("#availableDeposit").text(response.deposit.toFixed(2));
          $("#newDeposit").text(response.deposit.toFixed(2));
          $("#minDepositForm,#depositForm,#deleteDeposit").data("id", id);
          $("#depositType").change();
          $("#depositModal").modal("show");
        }
      },
    });
  });

  $("#minDepositForm").submit(function (e) {
    e.preventDefault();
    const form = $(this)[0];
    const id = $(this).data("id");
    const fd = new FormData(form);
    const value = fd.get("deposit");
    if (value === "") {
      $(this).addClass("was-validated");
      return;
    }
    $(this).removeClass("was-validated");
    $.post(
      "./includes/deposit.php",
      { minDeposit: value, id },
      function (data, textStatus, jqXHR) {
        if (!data.error) {
          Toast.fire("Deposito minimo actualizado", "", "success");
          $("#depositModal").modal("hide");
        }
      },
      "json"
    );
  });

  $("#depositType").change(function (e) {
    if ($(this).val() === "9") {
      $("#depositOther").prop("disabled", false);
    } else {
      $("#depositOther").prop("disabled", true);
    }

    if ($(this).val() === "6") {
      $("#depositAmount").prop("disabled", true);
    } else {
      $("#depositAmount").prop("disabled", false);
    }
  });
  $("#depositAmount").keyup(function () {
    const value = parseFloat($(this).val());
    const available = parseFloat($("#availableDeposit").text());
    const type = parseInt($("#depositType").val());
    if (isIncorrectAmount(value, type)) {
      $("#newDeposit").text(available.toFixed(2));
      $("#depositAmount").addClass("is-invalid");
      return;
    }
    $("#depositAmount").removeClass("is-invalid");
    const newDeposit = value + available;
    $("#newDeposit").text(newDeposit.toFixed(2));
  });

  $("#depositForm").submit(function (e) {
    e.preventDefault();
    const $this = $(this);
    const form = $this[0];
    const id = $this.data("id");
    const fd = new FormData(form);
    const available = parseFloat($("#availableDeposit").text());

    const type = parseInt(fd.get("type"));
    const otherDescription = fd.get("other");

    const amount = parseFloat(fd.get("amount"));
    if (isIncorrectAmount(amount, type)) {
      $("#depositAmount").addClass("is-invalid");
      return;
    }
    if (type === 6 && available <= 0) {
      Alert.fire({
        icon: "info",
        text: "No se puede hacer una devolucion cuando la cuenta esta en 0",
      });
      return;
    }

    if (type === 9 && otherDescription.length <= 0) {
      $("#depositOther").addClass("is-invalid");
      return;
    }
    const data = {
      id,
      amount,
      type,
      other: otherDescription,
    };

    $("#depositAmount,#depositOther").removeClass("is-invalid");
    $.post($this.prop("action"), data, function (data, textStatus, jqXHR) {
      const available = parseFloat($("#availableDeposit").text());
      const newDeposit = amount + available;
      $(`button[data-id=${id}] span`).text(
        type === 6 ? "0.00" : newDeposit.toFixed(2)
      );
      form.reset();
      Toast.fire("Cantidad actualizada", "", "success");

      console.log(data);
    });

    $("#depositModal").modal("hide");
  });

  $("#deleteDeposit").click(function (e) {
    const id = $(this).data("id");
    ConfirmationAlert.fire({
      title:
        __LANG === "es"
          ? "Seguro que desea poner el balance en 0?"
          : "Are you sure you want to update the balance to 0?",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: "POST",
          url: "./includes/deposit.php",
          data: { id, deleteDeposit: true },
          dataType: "json",
          success: function (response) {
            console.log({ response });
            if (!response.error) {
              $(`button[data-id=${id}] span`).text("0.00");
              $("#depositModal").modal("hide");
              Toast.fire("Deposito eliminado!", "", "success");
            }
          },
          error: function (error) {
            console.log({ error });
          },
        });
      }
    });
  });

  $(document).on("click", ".editCharge", function (e) {
    const id = $(this).data("id");
    $.ajax({
      type: "GET",
      url: "./includes/editCharge.php",
      data: { id },
      dataType: "json",
      success: function (response) {
        if (response.error) {
          Toast.fire("Hubo un error");
        } else {
          $("#editChargeTo").val(response.chargeTo);
          $("#editChargeDate").val(response.date);
          $("#editChargeDescription").val(response.description);
          $("#editChargeId").val(response.id);

          $("#editChargeAmount").val(response.amount.toFixed(2));
          $("#editChargeModal").modal("show");
        }
      },
    });
  });
  $(document).on("click", ".editPayment", function (e) {
    const id = $(this).data("id");
    console.log("payment clicked");
    $.ajax({
      type: "GET",
      url: "./includes/editPayment.php",
      data: { id },
      dataType: "json",
      success: function (response) {
        if (response.error) {
          Toast.fire("Hubo un error");
        } else {
          $("#editPaymentTo").val(response.chargeTo);
          $("#editPaymentBash").val(response.bash);
          $("#editPaymentChargeDate").val(response.charge_date);
          $("#editPaymentPaymentDate").val(response.payment_date);
          $("#editPaymentDate").val(response.date);
          $("#editPaymentTime").val(response.time);
          $("#editPaymentDescription").val(response.description);
          $("#editPaymentId").val(response.id);
          $("#editPaymentUser").val(response.user);
          $("#editPaymentPaymentType").val(response.paymentType);
          $("#editPaymentChkNum").val(response.checkNumber);
          $("#editPaymentComment").val(response.comment);
          $("#editPaymentChangeDate").val(response.change_date || "N\\A");
          $("#editPaymentCode").val(response.code);

          $("#editPaymentAmount").val(response.amount.toFixed(2));
          $("#editPaymentReturnedCheck").change();
          $("#editPaymentModal").modal("show");
        }
      },
    });
  });

  $("#editPaymentReturnedCheck").change(function () {
    if ($(this).prop("checked")) {
      $(this).parents(".form-group").next().removeClass("hidden");
    } else {
      $(this).parents(".form-group").next().addClass("hidden");
    }
  });

  $(document).on("click", ".delete", function (e) {
    const id = $(this).data("id");
    const parentTr = $(this).parents("tr");

    ConfirmationAlert.fire({
      title:
        __LANG === "es"
          ? "Seguro que desea borrarlo?"
          : "Are you sure you want to delete it?",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: "POST",
          url: "./includes/delete.php",
          data: { id },
          dataType: "json",
          success: function (response) {
            if (response.success) {
              parentTr.remove();
              displayAmounts();
              toggleMonthButtons();
              Toast.fire("Eliminado!", "", "success");
            }
          },
        });
      }
    });
  });

  $("#code").change(function (e) {
    console.log("changed");
    const desc = $("#code option:selected").text();
    $("#codeDescription").val(desc);
  });
  $("#addChargeModal").on("show.bs.modal", function (event) {
    const desc = $("#code option:selected").text();
    $("#codeDescription").val(desc);
    $("#month").val(_month);
  });

  $("#monthsButtons button").click(function (e) {
    e.preventDefault();
    const btn = e.target;
    const month = btn.dataset.month;
    if (month === _month) return;
    _month = month;
    $("#monthToPay").val(month);
    setSearchParams("month", month);
    $("#monthsButtons button").removeClass("active");
    $(".monthTable").addClass("hidden");
    $(btn).addClass("active");
    $(`#table${month}`).removeClass("hidden");

    displayAmounts();
  });

  $("#paymentModal").on("show.bs.modal", function (event) {
    const date = new Date().toISOString().split("T")[0];
    const total = Number($("#totalBalance").text());
    console.log(total);
    changePaymentTotal(total);
    $("#paymentDate").val(date);
    _parcialPaymentTotal = 0;
    $("#monthToPay").val(_month);
    $("#paymentAccountId").val($("#paymentModal").data("accountId"));

    $("#paymentMode").change();
    $("#paymentButton").prop("disabled", total <= 0);
  });

  $("#paymentMode").change(function (event) {
    const value = $(this).val();
    if (value === "parcial") {
      $("#parcialPaymentDebts").text("");
      changePaymentTotal(0);
      _parcialPaymentTotal = 0;
      let debts = {};
      $(`#table${_month} tr`).each(function (index, tr) {
        const id = tr.dataset.id;
        const debt = parseFloat($(tr).find(".debt").text());
        const paid = parseFloat($(tr).find(".payment").text());
        const grade = $(tr).find("td").eq(0).text();
        const desc = $(tr).find("td").eq(1).text();
        const label = `${grade} ${desc}`;
        if (!debts[`${id}-${grade}`]) {
          debts = {
            ...debts,
            [`${id}-${grade}`]: {
              label,
              debt: debt,
              paid: paid,
              code: id,
              grade,
            },
          };
        } else {
          debts = {
            ...debts,
            [`${id}-${grade}`]: {
              ...debts[[`${id}-${grade}`]],
              debt: debts[`${id}-${grade}`].debt + debt,
              paid: debts[`${id}-${grade}`].paid + paid,
            },
          };
        }
      });
      Object.entries(debts).forEach(([_, debt], index) => {
        const sum = debt.debt - debt.paid;
        if (sum > 0) {
          $("#parcialPaymentDebts")
            .removeClass("hidden")
            .append(
              ParcialPaymentDebt({
                label: debt.label,
                amount: sum,
                idNumber: index,
                code: debt.code,
                grade: debt.grade,
              })
            );
        }
      });
    } else {
      const { totalBalance } = calculateMonthTotal({ month: _month });
      changePaymentTotal(totalBalance);
      $("#parcialPaymentDebts").addClass("hidden").text("");
    }
  });

  $(document).on("change", ".parcialPaymentDebt", function (event) {
    const amount = parseFloat($(this).data("amount"));
    const $input = $(this).parents(".form-group").next().find("input");
    $input.prop("disabled", !$(this).prop("checked"));

    if ($(this).prop("checked")) {
      _parcialPaymentTotal += amount;
    } else {
      _parcialPaymentTotal -= amount;
      $input.val(amount);
    }
    changePaymentTotal(_parcialPaymentTotal);

    $input.change(function (e) {
      changePaymentTotal(parseFloat($(this).val()));
    });
  });

  /* -------------------------------- Functions ------------------------------- */

  function addChargeToTable(
    monthToAdd,
    { id, grade, description, date, debt, mt }
  ) {
    const tr = `
    <tr data-id="<?= $charge->codigo ?>">
        <th scope="row">${id}</th>
        <td>${grade}</td>
        <td>${description}</td>
        <td>${date}</td>
        <td class="text-right debt">${parseFloat(debt).toFixed(2)}</td>
        <td class="text-right payment">0.00</td>
        <td></td>
        <td></td>
        <td></td>
        <td class="text-right">
            <i data-id="${mt}" role="button" class="delete fa-solid fa-trash text-danger pointer-cursor"></i>
            <i data-id="${mt}" role="button" class="editCharge fa-solid fa-pen-to-square text-info pointer-cursor"></i>
        </td>
    </tr>`;
    $(`#table${monthToAdd}`).append(tr);
    displayAmounts();
    toggleMonthButtons();
  }
  async function getLatePaymentInfo() {
    const accountId = $("#accountId").val();
    const ajax = await $.ajax({
      type: "GET",
      url: "./includes/latePayment.php",
      data: { accountId },
      dataType: "json",
    });
    return ajax;
  }
  async function init() {
    const { observationType, alert, info } = await getLatePaymentInfo();
    if (observationType || alert) {
      let text =
        observationType === "Si"
          ? "Cheque rebotado"
          : observationType === "2"
          ? "Documento"
          : observationType === "3"
          ? info
          : "";
      text += text.length > 0 ? "<br/>" : "";
      text += alert ? "No puede pagar con cheque" : "";
      Alert.fire("Advertencia", text, "warning");
      $("#latePaymentButton")
        .addClass("btn-danger")
        .removeClass("btn-secondary");
    }
    setSearchParams("month", _month);
    displayAmounts();
    toggleMonthButtons();
  }

  function isIncorrectAmount(amount, type) {
    if (type === 6) return false;
    return (
      isNaN(amount) ||
      (type !== 5 && type !== 9 && parseFloat(amount) <= 0) ||
      ((type === 5 || type === 9) && parseFloat(amount) === 0)
    );
  }
  function changePaymentTotal(total) {
    $("#paymentTotal").text(Number(total).toFixed(2));
    $("#paymentButton").prop("disabled", Number(total) <= 0);
  }

  function ParcialPaymentDebt({ label, amount, idNumber, code, grade }) {
    return `
    <div class="form-row align-items-center">
        <div class="form-group mb-1 col-8">
            <div class="custom-control custom-checkbox">
                <input data-amount="${amount}" type="checkbox" value="${code}" class="custom-control-input parcialPaymentDebt" id="parcialPaymentDebt${idNumber}" name="parcialPaymentDebtsCodes[${idNumber}]">
                <input type="hidden" value="${grade}" class="custom-control-input parcialPaymentDebt" name="parcialPaymentDebtsGrades[${idNumber}]">
                <label class="custom-control-label w-100" for="parcialPaymentDebt${idNumber}">${label}</label>
            </div>
        </div>
        <div class="input-group input-group-sm  mb-1 col-4">                    
           <input type="number" max="${amount}" name="parcialPaymentDebtsAmounts[${idNumber}]" class="font-weight-bold form-control text-right" disabled value="${amount}"/>
           <div class="input-group-append">
            <small class="input-group-text text-muted">/${amount}</small>
          </div>
        </div>
    </div>  
    `;
  }

  function calculateMonthTotal({ month }) {
    let debts = [];
    let payments = [];
    $(`#table${month} tr`).each(function (index, tr) {
      debts.push(parseFloat($(tr).find(".debt").text()));
      payments.push(parseFloat($(tr).find(".payment").text()));
    });
    const totalDebts = debts.reduce((sum, debt) => sum + debt, 0);
    const totalPayments = payments.reduce((sum, payment) => sum + payment, 0);
    const totalBalance = totalDebts - totalPayments;

    return { totalPayments, totalDebts, totalBalance };
  }
  function displayAmounts() {
    const { totalPayments, totalDebts, totalBalance } = calculateMonthTotal({
      month: _month,
    });

    $("#totalDebts").text(totalDebts.toFixed(2));
    $("#totalPayments").text(totalPayments.toFixed(2));
    $("#totalBalance").text(totalBalance.toFixed(2));
  }

  function setSearchParams(key, value) {
    const url = new URL(window.location.href);
    url.searchParams.set(key, value);
    window.history.pushState({}, null, url.search);
  }

  function toggleMonthButtons({ month } = {}) {
    const months = month
      ? [month]
      : [
          "01",
          "02",
          "03",
          "04",
          "05",
          "06",
          "07",
          "08",
          "09",
          "10",
          "11",
          "12",
        ];
    months.forEach((month) => {
      const { totalBalance } = calculateMonthTotal({ month });
      if (totalBalance <= 0) {
        $(`button[data-month="${month}"]`)
          .addClass("btn-outline-primary")
          .removeClass("btn-outline-success");
      } else {
        $(`button[data-month="${month}"]`)
          .addClass("btn-outline-success")
          .removeClass("btn-outline-primary");
      }
    });
  }
});
