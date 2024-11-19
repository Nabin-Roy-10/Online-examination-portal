document.getElementById("questionForm").addEventListener('submit', function(event){
event.preventDefault();

let subject = document.getElementById("subjectName").value;
let modNum = document.getElementById("moduleNumber").value;
let question = document.getElementById("question").value;
let option1 = document.getElementById("option1").value;
let option2 = document.getElementById("option2").value;
let option3 = document.getElementById("option3").value;
let option4 = document.getElementById("option4").value;
let curr_ans = document.getElementById("currectAnswer").value;
let messageElement = document.getElementById("message");

if(subject && modNum && question && option1 && option2 && option3 && option4 && curr_ans){
	fetch('admin_createQuestion_php.php',{
		method:'POST',
		headers:{
			'Content-Type':'application/json'
		},
		body:JSON.stringify({
			subject:subject,
			modNum:modNum,
			question:question,
			option1:option1,
			option2:option2,
			option3:option3,
			option4:option4,
			curr_ans:curr_ans	
		})

	})
	.then(response=>response.json())
	.then(data=>{
		if(data.success){
			messageElement.textContent="submit successful. Enter your next question";
			setTimeout(() => {
                    	window.location.assign("createQuestions.html"); // Redirect after displaying success message
                }, 2000);
		}else{
			messageElement.textContent="error!!" + data.message;
		}
	})
	.catch(error=>{
		messageElement.textContent='An error occurred'+error.message;
	});
}else{
	messageElement.textContent='please all the fields';
}
});



