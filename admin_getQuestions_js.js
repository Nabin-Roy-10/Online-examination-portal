document.getElementById("questionForm").addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent default form submission

    let subject = document.getElementById("subjectName").value;
    let modNum = document.getElementById("moduleNumber").value;
    let messageElement = document.getElementById("message");

    if (subject && modNum) {
        fetch('admin_getQuestion_php.php', {
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
                            <th>Update</th>
                        </tr>`;
                questions.forEach(question => {
                    tableHtml += `
                        <tr>
                            <td>${question.sub_name}</td>
                            <td contenteditable="true">${question.question}</td>
                            <td contenteditable="true">${question.option1}</td>
                            <td contenteditable="true">${question.option2}</td>
                            <td contenteditable="true">${question.option3}</td>
                            <td contenteditable="true">${question.option4}</td>
                            <td contenteditable="true">${question.corr_ans}</td>
                            <td><button onclick="removeRow('${question.question}', this)">Remove</button></td>
                            <td><button onclick="updateRow(this)">Update</button></td>
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
        fetch('admin_getQuestion_php.php', {
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
                let row = btn.parentNode.parentNode;
                row.parentNode.removeChild(row); // Remove the row from the table
            } else {
                console.error('Failed to delete row:', data.message);
            }
        })
        .catch(error => console.error('Error deleting row:', error));
    }
}

function updateRow(btn) {
    // Select the row of the clicked button
    let row = btn.parentNode.parentNode;
    let cells = row.querySelectorAll("td");

    // Collect data from each editable cell
    let subject = cells[0].textContent;
    let question = cells[1].textContent;
    let option1 = cells[2].textContent;
    let option2 = cells[3].textContent;
    let option3 = cells[4].textContent;
    let option4 = cells[5].textContent;
    let corr_ans = cells[6].textContent;

    // Send update request to PHP script
    fetch('admin_getQuestion_php.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            action: 'update',
            subject: subject,
            question: question,
            option1: option1,
            option2: option2,
            option3: option3,
            option4: option4,
            corr_ans: corr_ans
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Question updated successfully');
        } else {
            alert('Failed to update question: ' + data.message);
        }
    })
    .catch(error => console.error('Error updating row:', error));
}
