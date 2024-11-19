document.getElementById("questionForm").addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission

    let subject = document.getElementById("subjectName").value;
    let modNum = document.getElementById("moduleNumber").value;
    let messageElement = document.getElementById("message");

    if (subject && modNum) {
        fetch('retrive.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                subject: subject,
                modNum: modNum
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let questions = data.data;
                let tableHtml = `
                    <table border='1'>
                        <tr>
                            <th>Subject Name</th>
                            <th>Question</th>
                            <th>Option 1</th>
                            <th>Option 2</th>
                            <th>Option 3</th>
                            <th>Option 4</th>
                            <th>Correct Answer</th>
                            <th>Remove</th>
                        </tr>`;
                questions.forEach(question => {
                    tableHtml += `
                        <tr>
                            <td>${question.sub_name}</td>
                            <td>${question.question}</td>
                            <td>${question.option1}</td>
                            <td>${question.option2}</td>
                            <td>${question.option3}</td>
                            <td>${question.option4}</td>
                            <td>${question.corr_ans}</td>
                            <td><button onclick="removeRow('${question.question}', this)">Remove</button></td>
                        </tr>`;
                });
                tableHtml += `</table>`;
                messageElement.innerHTML = tableHtml;
            } else {
                messageElement.textContent = "Error: " + data.message;
            }
        })
        .catch(error => {
            messageElement.textContent = 'An error occurred: ' + error.message;
        });
    } else {
        messageElement.textContent = 'Please fill in all the fields';
    }
});

function removeRow(question, btn) {
    if (confirm('Are you sure you want to remove this row?')) {
        fetch('retrive.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'delete',
                question: question
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the row from the table
                let row = btn.parentNode.parentNode;
                row.parentNode.removeChild(row);
            } else {
                console.error('Failed to delete row:', data.message);
            }
        })
        .catch(error => console.error('Error deleting row:', error));
    }
}
