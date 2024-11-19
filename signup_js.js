document.getElementById("registrationForm").addEventListener('submit',function(event){
	event.preventDefault();

	let username=document.getElementById("username").value;
	let email=document.getElementById("email").value;
	let memberType=document.getElementById("memberType").value;
	let password=document.getElementById("password").value;
	let messageElement=document.getElementById("message");

	if(username && email && memberType && password){
		fetch('signup_php.php',{
			method:'POST',
			headers:{
				'Content-Type' : 'application/json'
			},
			body:JSON.stringify({
				username:username,
				email:email,
				memberType:memberType,
				password:password
			})
		})
		.then(response=>response.json())
		.then(data=>{
			if(data.success){
				messageElement.textContent="registration successful";
				if(memberType=="Student"){
					window.location.href = 'student.html';
				}else if(memberType=="Teacher"){
                			window.location.href = 'teachers.html'; 
				}else if(memberType=="Admin"){
					window.location.href = 'admin_panel.html';
				}
			}else{
				messageElement.textContent="registration failed"+data.message;
			}
		})
		.catch(error=>{
			messageElement.textContent="An error occurred"+error.message;
		});
	}else{
		messageElement.textContent="fill all the forms";
	}
})