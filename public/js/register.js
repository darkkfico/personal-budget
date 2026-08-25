let password = document.getElementById("password");
let passwordC = document.getElementById("passwordC");
let passwordSee = document.getElementById("passwordSee");
let passwordCSee = document.getElementById("passwordCSee");

let PSC = 0;
let PSCC = 0;

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

passwordCSee.addEventListener("click", () => {
    if (PSCC % 2 == 0) {
        passwordCSee.classList.replace("fa-eye-slash", "fa-eye");
        passwordC.setAttribute("type", "text");
    } else {
        passwordCSee.classList.replace("fa-eye", "fa-eye-slash");
        passwordC.setAttribute("type", "password");
    }

    PSCC++;
});
