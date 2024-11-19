document.addEventListener("DOMContentLoaded", function() {
    // Fetch the session data when the student page loads
    fetch('get_student_session.php')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Display student details in the welcome message
            document.getElementById("welcomeMessage").textContent = `Welcome, ${data.sname} (${data.email})!`;
        } else {
            alert(data.message); // If session is not available, show an error
        }
    })
    .catch(error => console.error('Error fetching session data:', error));
});

// Existing code for submitting exam questions
document.getElementById("questionForm").addEventListener('submit', function(event) {
    event.preventDefault();

    let subject = document.getElementById("subjectName").value;
    let modNum = document.getElementById("moduleNumber").value;
    let messageElement = document.getElementById("message");

    if (subject && modNum) {
        fetch('submit_answers.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                subject: subject,
                modNum: modNum,
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let questions = data.data;
                let formHtml = `<form id="answersForm">`;
                questions.forEach((question, index) => {
                    formHtml += `
                        <div>
                            <p>${question.question}</p>
                            <input type="radio" name="question_${index}" value="${question.option1}"> ${question.option1}<br>
                            <input type="radio" name="question_${index}" value="${question.option2}"> ${question.option2}<br>
                            <input type="radio" name="question_${index}" value="${question.option3}"> ${question.option3}<br>
                            <input type="radio" name="question_${index}" value="${question.option4}"> ${question.option4}<br>
                            <input type="hidden" name="correct_answer_${index}" value="${question.corr_ans}">
                        </div>`;
                });
                formHtml += `<input type="submit" value="Submit Answers"></form>`;
                messageElement.innerHTML = formHtml;

                // Add event listener for submitting answers
                document.getElementById("answersForm").addEventListener('submit', function(event) {
                    event.preventDefault();
                    let formData = new FormData(event.target);
                    let userAnswers = {};
                    let correctAnswers = {};
                    for (let pair of formData.entries()) {
                        if (pair[0].startsWith('question_')) {
                            let questionIndex = pair[0].split('_')[1];
                            userAnswers[questionIndex] = pair[1];
                        } else if (pair[0].startsWith('correct_answer_')) {
                            let questionIndex = pair[0].split('_')[2];
                            correctAnswers[questionIndex] = pair[1];
                        }
                    }

                    let correctCount = 0;
                    let totalQuestions = Object.keys(correctAnswers).length;
                    Object.keys(userAnswers).forEach(index => {
                        if (userAnswers[index] === correctAnswers[index]) {
                            correctCount++;
                        }
                    });

                    alert(`You got ${correctCount} out of ${totalQuestions} correct`);
                });
            } else {
                messageElement.textContent = "Error: " + data.message;
            }
        })
        .catch(error => {
            console.error('Fetch error:', error); // Log fetch error
            messageElement.textContent = 'An error occurred: ' + error.message;
        });
    } else {
        messageElement.textContent = 'Please fill in all the fields';
    }
});
