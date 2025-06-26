<!DOCTYPE html>
<html>
<head>
    <title>Task Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div id="alert-container" class="container mt-3"></div>
<div class="container">
    <h1 class="mb-4">Task Manager</h1>

    <form id="task-form" class="mb-4">
        <div class="row g-2">
            <div class="col"><input type="text" class="form-control" name="title" id="title" placeholder="Title" required></div>
            <div class="col"><input type="text" class="form-control" name="description" id="description" placeholder="Description"></div>
            <div class="col">
                <select class="form-select" name="priority" id="priority">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
            <div class="col">
                <select class="form-select" name="status" id="status">
                    <option value="pending" selected>Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div class="col">
                <input type="date" class="form-control" name="due_date" id="due_date">
            </div>
            <input type="hidden" name="id" id="task-id">
            <div class="col">
                <button type="submit" class="btn btn-primary" id="task-submit-btn">Save Task</button>
            </div>
        </div>
    </form>

    <form id="filter-form" class="mb-4">
        <div class="row g-2">
            <div class="col">
                <select class="form-select" name="status">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div class="col">
                <select class="form-select" name="priority">
                    <option value="">All Priorities</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
            <input type="hidden" name="id" id="task-id">
            <div class="col">
                <button type="submit" class="btn btn-secondary">Filter</button>
            </div>
        </div>
    </form>


    <table class="table table-bordered" id="tasks-table">
        <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Status</th>
            <th>Priority</th>
            <th>Due Date</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
    <nav>
        <ul class="pagination" id="pagination"></ul>
    </nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    const apiBase = '/api/v1/tasks';

    async function fetchTasks(filters = {}) {
        try {
            const params = new URLSearchParams(filters).toString();
            const response = await axios.get(`${apiBase}?${params}`);
            const tasks = response.data.data;
            const meta = response.data.meta;
            const tbody = document.querySelector('#tasks-table tbody');
            const pagination = document.getElementById('pagination');

            tbody.innerHTML = '';
            tasks.forEach(task => {
                const row = document.createElement('tr');
                row.innerHTML = `
                <td>${task.id}</td>
                <td>${task.title}</td>
                <td>${task.status}</td>
                <td>${task.priority}</td>
                <td>${task.due_date ?? ''}</td>
                <td>
                    <button class="btn btn-primary btn-sm" onclick='editTask(${JSON.stringify(task)})'>Edit</button>
                    <button class="btn btn-danger btn-sm" onclick="deleteTask(${task.id})">Delete</button>
                </td>

            `;
                tbody.appendChild(row);
            });

            // Pagination
            pagination.innerHTML = '';
            if (meta && meta.links) {
                meta.links.forEach(link => {
                    const li = document.createElement('li');
                    li.className = `page-item ${link.active ? 'active' : ''} ${!link.url ? 'disabled' : ''}`;
                    li.innerHTML = `
                    <a class="page-link" href="#" data-url="${link.url}">
                        ${link.label.replace(/&laquo;|&raquo;/g, (m) => m === '&laquo;' ? '«' : '»')}
                    </a>
                `;
                    pagination.appendChild(li);
                });

                // Attach click events
                document.querySelectorAll('#pagination a').forEach(a => {
                    a.addEventListener('click', e => {
                        e.preventDefault();
                        const url = a.dataset.url;
                        if (url) {
                            const urlObj = new URL(url);
                            const page = urlObj.searchParams.get('page');
                            fetchTasks({ ...filters, page });
                        }
                    });
                });
            }

        } catch (error) {
            showAlert('Something went wrong', 'danger');
            console.error(error);
        }
    }

    document.getElementById('filter-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const filters = Object.fromEntries(formData.entries());
        fetchTasks(filters);
    });

    async function deleteTask(id) {
        if (!confirm('Are you sure you want to delete this task?')) return;
        try {
            await axios.delete(`${apiBase}/${id}`);
            showAlert('Task deleted successfully!');
            fetchTasks();
        } catch (error) {
            showAlert('Something went wrong', 'danger');
            console.error(error);
        }
    }

    document.getElementById('task-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const payload = Object.fromEntries(formData.entries());
        const taskId = payload.id;
        delete payload.id;

        try {
            if (taskId) {
                await axios.patch(`${apiBase}/${taskId}`, payload);
                showAlert('Task updated successfully!');
            } else {
                await axios.post(apiBase, payload);
                showAlert('Task created successfully!');
            }

            e.target.reset();
            document.getElementById('task-id').value = ''; // Clear hidden ID
            fetchTasks(); // Refresh the task table

        } catch (error) {
            showAlert('Failed to save task', 'danger');
            console.error(error);
        }
    });

    function editTask(task) {
        document.getElementById('task-id').value = task.id;
        document.getElementById('title').value = task.title;
        document.getElementById('description').value = task.description;
        document.getElementById('status').value = task.status;
        document.getElementById('priority').value = task.priority;
        if (task.due_date) {
            document.getElementById('due_date').value = task.due_date;
        }
        showAlert('You are editing a task.', 'info');
    }

    function showAlert(message, type = 'success') {
        const alertContainer = document.getElementById('alert-container');
        alertContainer.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    }

    fetchTasks();
</script>
</body>
</html>