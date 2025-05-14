let profile = document.querySelector(".header .flex .profile");

document.querySelector("#user-btn").onclick = () => {
  profile.classList.toggle("active");
  navbar.classList.remove("active");
};

let navbar = document.querySelector(".header .flex .navbar");

document.querySelector("#menu-btn").onclick = () => {
  navbar.classList.toggle("active");
  profile.classList.remove("active");
};

window.onscroll = () => {
  profile.classList.remove("active");
  navbar.classList.remove("active");
};

subImages = document.querySelectorAll(
  ".update-product .image-container .sub-images img"
);
mainImage = document.querySelector(
  ".update-product .image-container .main-image img"
);

subImages.forEach((images) => {
  images.onclick = () => {
    let src = images.getAttribute("src");
    mainImage.src = src;
  };
});

// document.addEventListener("DOMContentLoaded", function () {
//    const dropdownToggles = document.querySelectorAll(".dropdown-toggle");

//    dropdownToggles.forEach(function (toggle) {
//       toggle.addEventListener("click", function () {
//          const parent = this.closest(".dropdown");
//          parent.classList.toggle("open");
//       });
//    });
// });

document.getElementById("MaThue").addEventListener("blur", function () {
  const maThue = this.value;

  if (maThue.trim() !== "") {
    fetch("get_info_by_mathue.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: "MaThue=" + encodeURIComponent(maThue),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          document.getElementById("MaKH").value = data.MaKH;
          // Bạn có thể hiển thị NgayTraDK nếu cần
        } else {
          alert(data.message);
          document.getElementById("MaKH").value = "";
        }
      })
      .catch((error) => {
        console.error("Lỗi:", error);
      });
  }
});
