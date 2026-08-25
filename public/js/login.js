let password = document.getElementById("password");
let passwordSee = document.getElementById("passwordSee");

let PSC = 0;

passwordSee.addEventListener("click", () => {
    if (PSC % 2 == 0) {
        passwordSee.classList.replace("fa-eye-slash", "fa-eye");
        password.setAttribute("type", "text");
    } else {
        passwordSee.classList.replace("fa-eye", "fa-eye-slash");
        password.setAttribute("type", "password");
    }

    console.log(PSC);

    PSC++;
});