document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modal');
    const deleteModal = document.getElementById('deleteModal');
    const closeBtns = document.querySelectorAll('.close');
    const lessonForm = document.getElementById('lessonForm');
    const deleteForm = document.getElementById('deleteForm');
    
    document.querySelectorAll('.timetable td[data-dayid]').forEach(cell => {
        cell.addEventListener('click', function () {
            const dayid = this.dataset.dayid;
            const dayweek = this.dataset.dayweek;
            
            if (this.innerHTML.trim()) {
                const [lesson, teacher] = this.innerHTML.split('<br>');
                deleteForm.querySelector('input[name="lesson"]').value = lesson;
                deleteForm.querySelector('input[name="teacher"]').value = teacher;
                deleteForm.querySelector('input[name="dayid"]').value = dayid;
                deleteForm.querySelector('input[name="dayweek"]').value = dayweek;
                deleteModal.style.display = 'flex';
            } else {
                lessonForm.querySelector('input[name="dayid"]').value = dayid;
                lessonForm.querySelector('input[name="dayweek"]').value = dayweek;
                modal.style.display = 'flex';
            }
        });
    });

    closeBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            modal.style.display = 'none';
            deleteModal.style.display = 'none';
        });
    });

    window.addEventListener('click', event => {
        if (event.target === modal || event.target === deleteModal) {
            modal.style.display = 'none';
            deleteModal.style.display = 'none';
        }
    });

    lessonForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(lessonForm);

        fetch('ajax_add_lesson.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cell = document.querySelector(`td[data-dayid="${data.dayid}"][data-dayweek="${data.dayweek}"]`);
                cell.innerHTML = `${data.lesson}<br>${data.teacher}`;
                modal.style.display = 'none';
            } else {
                alert('Ошибка: ' + data.message);
            }
        })
        .catch(error => console.error('Ошибка:', error));
    });

    deleteForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(deleteForm);
    
        fetch('ajax_delete_lesson.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cell = document.querySelector(`td[data-dayid="${data.dayid}"][data-dayweek="${data.dayweek}"]`);
                cell.innerHTML = ''; // Очищаем ячейку
                deleteModal.style.display = 'none'; // Закрываем модальное окно
                alert('Урок успешно удалён!'); // Уведомляем пользователя об успехе
            } else {
                showError(data.message); // Отображаем сообщение об ошибке на странице
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
            showError('Произошла ошибка при удалении урока.'); // Отображаем общее сообщение об ошибке
        });
    });
    
    // Функция для отображения ошибки
    function showError(message) {
        const errorContainer = document.getElementById('errorContainer'); // Предполагается, что есть контейнер для ошибок
        errorContainer.textContent = message;
        errorContainer.style.display = 'block'; // Показываем контейнер с ошибкой
    }});
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modal');
    const deleteModal = document.getElementById('deleteModal');
    const closeBtns = document.querySelectorAll('.close');
    const lessonForm = document.getElementById('lessonForm');
    const deleteForm = document.getElementById('deleteForm');
    
    document.querySelectorAll('.timetable td[data-dayid]').forEach(cell => {
        cell.addEventListener('click', function () {
            const dayid = this.dataset.dayid;
            const dayweek = this.dataset.dayweek;
            
            if (this.innerHTML.trim()) {
                const [lesson, teacher] = this.innerHTML.split('<br>');
                deleteForm.querySelector('input[name="lesson"]').value = lesson;
                deleteForm.querySelector('input[name="teacher"]').value = teacher;
                deleteForm.querySelector('input[name="dayid"]').value = dayid;
                deleteForm.querySelector('input[name="dayweek"]').value = dayweek;
                deleteModal.style.display = 'flex';
            } else {
                lessonForm.querySelector('input[name="dayid"]').value = dayid;
                lessonForm.querySelector('input[name="dayweek"]').value = dayweek;
                modal.style.display = 'flex';
            }
        });
    });

    closeBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            modal.style.display = 'none';
            deleteModal.style.display = 'none';
        });
    });

    window.addEventListener('click', event => {
        if (event.target === modal || event.target === deleteModal) {
            modal.style.display = 'none';
            deleteModal.style.display = 'none';
        }
    });

    lessonForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(lessonForm);

        fetch('ajax_add_lesson.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cell = document.querySelector(`td[data-dayid="${data.dayid}"][data-dayweek="${data.dayweek}"]`);
                cell.innerHTML = `${data.lesson}<br>${data.teacher}`;
                modal.style.display = 'none';
            } else {
                alert('Ошибка: ' + data.message);
            }
        })
        .catch(error => console.error('Ошибка:', error));
    });

    deleteForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(deleteForm);
    
        fetch('ajax_delete_lesson.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cell = document.querySelector(`td[data-dayid="${data.dayid}"][data-dayweek="${data.dayweek}"]`);
                cell.innerHTML = ''; // Очищаем ячейку
                deleteModal.style.display = 'none'; // Закрываем модальное окно
                alert('Урок успешно удалён!'); // Уведомляем пользователя об успехе
            } else {
                showError(data.message); // Отображаем сообщение об ошибке на странице
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
            showError('Произошла ошибка при удалении урока.'); // Отображаем общее сообщение об ошибке
        });
    });
    
    // Функция для отображения ошибки
    function showError(message) {
        const errorContainer = document.getElementById('errorContainer'); // Предполагается, что есть контейнер для ошибок
        errorContainer.textContent = message;
        errorContainer.style.display = 'block'; // Показываем контейнер с ошибкой
    }});
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modal');
    const deleteModal = document.getElementById('deleteModal');
    const closeBtns = document.querySelectorAll('.close');
    const lessonForm = document.getElementById('lessonForm');
    const deleteForm = document.getElementById('deleteForm');
    
    document.querySelectorAll('.timetable td[data-dayid]').forEach(cell => {
        cell.addEventListener('click', function () {
            const dayid = this.dataset.dayid;
            const dayweek = this.dataset.dayweek;
            
            if (this.innerHTML.trim()) {
                const [lesson, teacher] = this.innerHTML.split('<br>');
                deleteForm.querySelector('input[name="lesson"]').value = lesson;
                deleteForm.querySelector('input[name="teacher"]').value = teacher;
                deleteForm.querySelector('input[name="dayid"]').value = dayid;
                deleteForm.querySelector('input[name="dayweek"]').value = dayweek;
                deleteModal.style.display = 'flex';
            } else {
                lessonForm.querySelector('input[name="dayid"]').value = dayid;
                lessonForm.querySelector('input[name="dayweek"]').value = dayweek;
                modal.style.display = 'flex';
            }
        });
    });

    closeBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            modal.style.display = 'none';
            deleteModal.style.display = 'none';
        });
    });

    window.addEventListener('click', event => {
        if (event.target === modal || event.target === deleteModal) {
            modal.style.display = 'none';
            deleteModal.style.display = 'none';
        }
    });

    lessonForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(lessonForm);

        fetch('ajax_add_lesson.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cell = document.querySelector(`td[data-dayid="${data.dayid}"][data-dayweek="${data.dayweek}"]`);
                cell.innerHTML = `${data.lesson}<br>${data.teacher}`;
                modal.style.display = 'none';
            } else {
                alert('Ошибка: ' + data.message);
            }
        })
        .catch(error => console.error('Ошибка:', error));
    });

    deleteForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(deleteForm);
    
        fetch('ajax_delete_lesson.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cell = document.querySelector(`td[data-dayid="${data.dayid}"][data-dayweek="${data.dayweek}"]`);
                cell.innerHTML = ''; // Очищаем ячейку
                deleteModal.style.display = 'none'; // Закрываем модальное окно
                alert('Урок успешно удалён!'); // Уведомляем пользователя об успехе
            } else {
                showError(data.message); // Отображаем сообщение об ошибке на странице
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
            showError('Произошла ошибка при удалении урока.'); // Отображаем общее сообщение об ошибке
        });
    });
    
    // Функция для отображения ошибки
    function showError(message) {
        const errorContainer = document.getElementById('errorContainer'); // Предполагается, что есть контейнер для ошибок
        errorContainer.textContent = message;
        errorContainer.style.display = 'block'; // Показываем контейнер с ошибкой
    }});
