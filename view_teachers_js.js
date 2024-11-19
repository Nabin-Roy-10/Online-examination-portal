        document.getElementById('viewTeachersBtn').addEventListener('click', function() {
            fetchTeachers();
        });
	document.getElementById('viewStudentsBtn').addEventListener('click', function() {
            fetchStudents();
        });
        function fetchTeachers() {
            fetch('view_teachers_php.php') // PHP script to fetch teacher data
                .then(response => response.json())
                .then(data => {
                    const teacherListDiv = document.getElementById('teacherList');
                    teacherListDiv.innerHTML = ''; // Clear previous data

                    if (data.success) {
                        const table = document.createElement('table');
                        const header = table.createTHead();
                        const headerRow = header.insertRow(0);
                        const headers = ['User ID', 'Username', 'Email', 'Member Type', 'Created At', 'Updated At']; // All relevant headers

                        headers.forEach((text) => {
                            const th = document.createElement('th');
                            th.textContent = text;
                            headerRow.appendChild(th);
                        });

                        const body = table.createTBody();
                        data.teachers.forEach(teacher => {
                            const row = body.insertRow();
                            row.insertCell(0).textContent = teacher.userId; // Adjust these based on your actual column names
                            row.insertCell(1).textContent = teacher.username;
                            row.insertCell(2).textContent = teacher.email;
                            row.insertCell(3).textContent = teacher.memberType;
                            row.insertCell(4).textContent = teacher.created_at; // Format if necessary
                            row.insertCell(5).textContent = teacher.updated_at; // Format if necessary
                        });

                        teacherListDiv.appendChild(table);
                    } else {
                        teacherListDiv.textContent = data.message;
                    }
                })
                .catch(error => {
                    console.error('Error fetching teachers:', error);
                });
        }

	function fetchStudents() {
            fetch('view_students_php.php') // PHP script to fetch teacher data
                .then(response => response.json())
                .then(data => {
                    const teacherListDiv = document.getElementById('teacherList');
                    teacherListDiv.innerHTML = ''; // Clear previous data

                    if (data.success) {
                        const table = document.createElement('table');
                        const header = table.createTHead();
                        const headerRow = header.insertRow(0);
                        const headers = ['User ID', 'Username', 'Email', 'Member Type', 'Created At', 'Updated At']; // All relevant headers

                        headers.forEach((text) => {
                            const th = document.createElement('th');
                            th.textContent = text;
                            headerRow.appendChild(th);
                        });

                        const body = table.createTBody();
                        data.teachers.forEach(teacher => {
                            const row = body.insertRow();
                            row.insertCell(0).textContent = teacher.userId; // Adjust these based on your actual column names
                            row.insertCell(1).textContent = teacher.username;
                            row.insertCell(2).textContent = teacher.email;
                            row.insertCell(3).textContent = teacher.memberType;
                            row.insertCell(4).textContent = teacher.created_at; // Format if necessary
                            row.insertCell(5).textContent = teacher.updated_at; // Format if necessary
                        });

                        teacherListDiv.appendChild(table);
                    } else {
                        teacherListDiv.textContent = data.message;
                    }
                })
                .catch(error => {
                    console.error('Error fetching teachers:', error);
                });
        }

  