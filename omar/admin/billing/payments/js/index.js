$(document).ready(function () {
  let _month = document.querySelector("#monthsButtons button.active").dataset
    .month;
  let _parcialPaymentTotal = 0;

  init();

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
            if (!response.error) {
              $(`button[data-id=${id}] span`).text("0.00");
              $("#depositModal").modal("hide");
              Toast.fire("Deposito eliminado!", "", "success");
            }
          },
        });
      }
    });
  });

  $(".editCharge").click(function (e) {
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
  $(".editPayment").click(function (e) {
    const id = $(this).data("id");
    console.log("payment clicked");
    $.ajax({
      type: "GET",
      url: "./includes/editPayment.php",
      data: { id },
      dataType: "json",
      success: function (response) {
        console.log({ response });
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

  $(".delete").click(function (e) {
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
    $("#accountId").val($("#paymentModal").data("accountId"));

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
      console.log("changed");
    });
  });

  function init() {
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
