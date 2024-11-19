document.getElementById("loginForm").addEventListener('submit', function(event) {
    event.preventDefault();

    let email = document.getElementById("email").value;
    let memberType = document.getElementById("memberType").value;
    let password = document.getElementById("password").value;
    let messageElement = document.getElementById("message");

    if (email && memberType && password) {
        fetch('login_php.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: email,
                memberType: memberType,
                password: password
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageElement.textContent = "Login successful";

                if(memberType=="Student"){
					window.location.href = 'student.html';
				}else if(memberType=="Teacher"){
                			window.location.href = 'teachers.html'; 
				}else{
					window.location.href = 'admin.php';
				}
            } else {
                messageElement.textContent = data.message;
            }
        })
        .catch(error => {
            messageElement.textContent = "An error occurred: " + error.message;
        });
    } else {
        messageElement.textContent = "Please fill all the fields.";
    }
});
