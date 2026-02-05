function hasSpecialCharacters() {
    let regex = /[^A-Za-z0-9]/;
    let inputField = document.getElementsByClassName("inputField");

    for (let i = 0; i < inputField.length; i++) {
        if (inputField.item(i) === document.activeElement) {
            if (regex.test(inputField.item(i).value)) {
                document.getElementsByClassName("inputCheck").item(i).textContent = "specials characters are not allowed";
            } else {
                document.getElementsByClassName("inputCheck").item(i).textContent = "";
            }
        }
    }
}

function isSecure() {
    let regexUppercase = /[A-Z]/;
    let regexLowercase = /[a-z]/;
    let regexSpecialChar = /[^A-Za-z0-9]/;
    let regexNumber = /[0-9]/;
    let inputField = document.getElementsByClassName("inputField");

    for (let i = 0; i < inputField.length; i++) {
        if (inputField.item(i) === document.activeElement) {
            if (regexUppercase.test(inputField.item(i).value) && regexLowercase.test(inputField.item(i).value) &&
                regexSpecialChar.test(inputField.item(i).value) && regexNumber.test(inputField.item(i).value) && (inputField.item(i).value.length > 7)) {
                document.getElementsByClassName("inputCheck").item(i).style.color = "#1b993b";
                document.getElementsByClassName("inputCheck").item(i).textContent = "Password ( Uppercase, Lowercase, Special Character, Number ) : Strong";
            } else if ((regexUppercase.test(inputField.item(i).value) || regexSpecialChar.test(inputField.item(i).value)) &&
                (regexLowercase.test(inputField.item(i).value) || regexNumber.test(inputField.item(i).value)) && (inputField.item(i).value.length > 7)) {
                document.getElementsByClassName("inputCheck").item(i).style.color = "orange";
                document.getElementsByClassName("inputCheck").item(i).textContent = "Password ( Uppercase, Lowercase, Special Character, Number ) : Medium";
            } else {
                document.getElementsByClassName("inputCheck").item(i).style.color = "#555555";
                document.getElementsByClassName("inputCheck").item(i).textContent = "Password ( Uppercase, Lowercase, Special Character, Number ) : Weak";
            }
        }
    }

    doesMacth();
}

function doesMacth() {
    let firstSet = document.getElementById("firstSet");
    let secondSet = document.getElementById("secondSet");
    let passwordChecking = document.getElementById("passwordChecking");

    if ((firstSet.value.length > 0) && (secondSet.value.length > 0)) {
        if (firstSet.value.localeCompare(secondSet.value) == 0) {
            passwordChecking.textContent = "Passwords match";
            passwordChecking.style.color = "#1b993b";
        } else {
            passwordChecking.textContent = "Passwords do not match";
            passwordChecking.style.color = "red";
        }
    } else {
        passwordChecking.textContent = "";
        passwordChecking.style.color = "red";
    }
}