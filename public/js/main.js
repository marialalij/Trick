

$("#deleteTrickModal").on("show.bs.modal", function (e) {
    $(this).find("#deleteTrick").attr("href", $(e.relatedTarget).data("href"));
});


$("#deleteModal").on("show.bs.modal", function (e) {
    $(this).find("#deleteComment").attr("href", $(e.relatedTarget).data("href"));
});

/* ******** trick page ****** */

$("#trickPage #trickMedia button").click(function (e) {
    $("#trickPage #trickMedia .media-slider").css("display", "block");
    $(this).css("display", "none");
});

$(".trick-media").click(function () {
    var trickId = $(this).attr("id");
    var carouselId = "carousel" + trickId;
    $(".carousel-item[id =" + carouselId + "]").addClass("active");
})

$("#modalGallery").on("hide.bs.modal", function (e) {
    $(".carousel-item").removeClass("active");
})

$(function () {

    /* ****** LoadMore Tricks and LoadLess trick buttons ***** */

    var tricksPerPage = 5;
    var commentsPerPage = 10;

    var tricks = $("div.trick-card-div");
    $("#arrowUp").hide();
    $("#loadLessTricksBtn").hide();
    if (tricks.length <= tricksPerPage) {
        $("#loadMoreTricksBtn").hide();
    }

    for (var i = tricksPerPage; i <= tricks.length - 1; i++) {
        tricks[i].remove();
    }

    $("#loadMoreTricksBtn").on("click", function (e) {
        e.preventDefault();
        tricksPerPage += 5;
        for (var i = 0; i <= tricksPerPage - 1; i++) {
            $("#trickList").append(tricks[i]);
        }
        if (tricks.length <= tricksPerPage) {
            $("#loadLessTricksBtn").show();
            $("#loadMoreTricksBtn").hide();
        }
        if (tricksPerPage >= 15) {
            $("#arrowUp").show();
        }
    });

    $("#loadLessTricksBtn").on("click", function (e) {
        e.preventDefault();
        tricksPerPage = 5;
        for (var i = tricksPerPage; i <= tricks.length - 1; i++) {
            tricks[i].remove();
        }
        $("#loadLessTricksBtn").hide();
        $("#loadMoreTricksBtn").show();
        $("#arrowUp").hide();

    });

    /* ****** LoadMore comments button ***** */

    var comments = $("div.trick-comment");
    if (comments.length <= commentsPerPage) {
        $("#loadMoreCommentsBtn").hide();
    }

    for (var i = commentsPerPage; i <= comments.length - 1; i++) {
        comments[i].remove();
    }

    $("#loadMoreCommentsBtn").on("click", function (e) {
        e.preventDefault();
        commentsPerPage += 5;
        for (var i = 0; i <= commentsPerPage - 1; i++) {
            $("#trickComments").append(comments[i]);
        }
        if (comments.length <= commentsPerPage) {
            $("#loadMoreCommentsBtn").hide();
        }
    });



    /* ********** Responsive ********* */



});
