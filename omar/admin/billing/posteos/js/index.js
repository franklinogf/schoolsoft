$(document).ready(function () {
  let post = null;
  let posts = null;
  $("input:radio[name=paymentType]").change(function (e) {
    if ($(this).val() === "automatico") {
      $("#dayOfPaymentDiv").removeClass("invisible");
    } else {
      $("#dayOfPaymentDiv").addClass("invisible");
    }
  });

  $("#add").click(function (e) {
    if ($("#student").val() === "") {
      $("#student").addClass("is-invalid");
      return;
    }
    if (
      (post === null && $("#amount").val() === "") ||
      $("#amount").val() === "" ||
      parseFloat($("#amount").val()) <= 0
    ) {
      $("#amount").addClass("is-invalid");
      return;
    }
    $("#student,#amount").removeClass("is-invalid");
    const jsonData = {
      addPost: $("#postId").val(),
      post: post,
      budget: $("#budgets").val(),
      student: $("#student").val(),
      amount: $("#amount").val(),
    };
    console.log({
      jsonData,
    });

    $.post(
      "./includes/posteos_data.php",
      jsonData,
      function (data) {
        getPosts({ student: $("#student").val(), budget: $("#budgets").val() });
        $("#totalAmount").text(data.totalAmount);
      },
      "json"
    );
  });

  $(".form").submit(function (event) {
    event.preventDefault();
    if (!$(this)[0].checkValidity()) {
      event.stopPropagation();
      $(this).addClass("was-validated");
      return false;
    }
    const type = $("#savePaymentMethod").data("type");
    const paymentType = $("input:radio[name=paymentType]:checked").val();
    let jsonData = {
      type,
      paymentType,
      account: $("#account").val(),
      email: $("#email").val(),
    };
    if ($(this).prop("id") === "cardForm") {
      jsonData = {
        ...jsonData,
        addPaymentMethod: "tarjeta",
        name: $("#cc-name").val(),
        number: $("#cc-number").cleanVal(),
        expiration: $("#cc-expiration").cleanVal(),
        cvv: $("#cc-cvv").val(),
        zip: $("#cc-zip").val(),
      };
    } else {
      jsonData = {
        ...jsonData,
        addPaymentMethod: "ach",
        name: $("#ach-name").val(),
        accountType: $("#ach-type").val(),
        number: $("#ach-number").val(),
        routeNumber: $("#ach-route").val(),
        zip: $("#ach-zip").val(),
      };
    }
    if (type === "update") {
      jsonData.id = $("#postId").val();
    }
    if (paymentType === "automatico") {
      jsonData.dayOfPayment = $("#dayOfPayment").val();
    }
    console.log(jsonData);

    $.post(
      "./includes/posteos_data.php",
      jsonData,
      function (data, textStatus, jqXHR) {
        console.log(data);
        $("#savePaymentMethod")
          .data("type", "update")
          .text("Actualizar metodo de pago");
        $("#paymentMethodText").removeClass("d-none");
        if (type === "save") $("#postId").val(data);
      }
    );
  });

  $("#savePaymentMethod").click(function (event) {
    if ($("#email").val().length > 0) {
      $("#email").removeClass("is-invalid");
      if ($("#cardMethod").hasClass("active")) {
        $("#cardForm").submit();
      } else {
        $("#achForm").submit();
      }
    } else {
      $("#email").addClass("is-invalid");
    }
  });

  $("#cc-expiration").mask("00/00", {
    placeholder: "MM/YY",
  });

  $("#cc-cvv").mask("0009", {
    placeholder: "123",
  });
  $(".justText").mask("Z", {
    translation: {
      Z: {
        pattern: /[A-Za-z ]/,
        recursive: true,
      },
    },
  });
  $(".justNumber").mask("0#");
  $("#cc-number").mask("0000 0000 0000 0000");
  $(".zip").mask("00000", {
    placeholder: "12345",
  });
  $("#amount").mask("#.##", {
    reverse: true,
    selectOnFocus: true,
  });

  $("#money").change(function (event) {
    if ($(this).val().length > 0) {
      total = parseFloat($(this).val()).toFixed(2);
      $(this).val(total);
    }
  });

  $("#budgets").change(function (e) {
    const budget = $(this).val();
    const budgetPost = posts.find((post) => post.budget.toString() === budget);
    if (budgetPost) {
      $("#amount").val(budgetPost.amount);
      post = budgetPost.id;
    } else {
      $("#amount").val("");
      post = null;
    }
  });
  $("#student").change(function (e) {
    const student = $(this).val();
    post = null;
    $("#add").prop("disabled", true).addClass("disabled");
    if (student === "") return;
    $("#add").prop("disabled", false).removeClass("disabled");
    const budget = $("#budgets").val();

    getPosts({ student, budget });
  });

  $("#postsList").on("click", ".delete", function (e) {
    const id = $(this).data("id");
    $.post(
      "./includes/posteos_data.php",
      { deletePost: id, posteoId: $("#postId").val() },
      function (data) {
        getPosts({ student: $("#student").val(), budget: $("#budgets").val() });
        $("#totalAmount").text(data.totalAmount);
      },
      "json"
    );
  });

  function getPosts({ student, budget }) {
    $.get(
      "./includes/posteos_data.php",
      {
        getPosts: $("#postId").val(),
        student,
      },
      function (data) {
        posts = data;
        console.log({ data });
        const budgetPost = data.find(
          (post) => post.budget.toString() === budget
        );
        console.log(budgetPost);
        if (budgetPost) {
          $("#amount").val(budgetPost.amount);
          post = budgetPost.id;
        } else {
          $("#amount").val("");
          post = null;
        }
        $("#postsList").empty();
        data.forEach((post) => {
          $("#postsList").append(
            PostCard({
              id: post.id,
              amount: post.amount,
              description: post.description,
              name: post.name,
            })
          );
        });
      },
      "json"
    );
  }
  function PostCard({ id, name, amount, description }) {
    return `
    <div class="list-group-item">
        <div class="d-flex justify-content-between mb-4">
            <h6 class="mb-1">${name}</h6>
            <span class="badge badge-info align-middle" style="line-height: initial !important;">$${amount}</span>
        </div>
        
        <div class="d-flex justify-content-between">
        <p class="mb-0">${description}</p>
        <button class="btn btn-danger btn-sm delete" data-id="${id}">Borrar</button>
        </div>
    </div>`;
  }
});
