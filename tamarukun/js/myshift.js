document.addEventListener('DOMContentLoaded', function() {
    var dialog = document.getElementById("dialog");

    var currentMonth;
    var currentYear;

    // Function to update the calendar days based on the current month and year
    function updateCalendarDays(year, month) {
        var daysList = document.querySelector('.days');
        // Clear the current days in the calendar
        daysList.innerHTML = '';

        // Get the number of days in the current month
        var numberOfDays = new Date(year, month, 0).getDate();

        // Get today's date
        var today = new Date();
        var currentDay = today.getDate();

        // Create the list of days for the current month
        for (var i = 1; i <= numberOfDays; i++) {
            var dayElement = document.createElement('li');
            var spanElement = document.createElement('span');
            spanElement.textContent = i;
            dayElement.appendChild(spanElement);
            daysList.appendChild(dayElement);

            // Add the data-day attribute to store the day number
            dayElement.setAttribute('data-day', i);

            // Check if the day is today and add the 'active' class to its span
            if (i === currentDay && month === today.getMonth() + 1 && year === today.getFullYear()) {
                spanElement.classList.add('active');
            }

            // Attach click event to each day
            attachDayClickEvent(dayElement);
        }
    }

    // Function to attach click event to each day
    function attachDayClickEvent(dayElement) {
        var noteDiv = dayElement.querySelector('.note');

        dayElement.addEventListener('click', function() {
            var selectedDay = this;
            var memoInput = document.getElementById('memoInput');
            var nameInput = document.getElementById('nameInput');
            var messageInput = document.getElementById('messageInput');

            var existingMemo = selectedDay.getAttribute('data-memo');
            var existingName = selectedDay.getAttribute('data-name');
            var existingMessage = selectedDay.getAttribute('data-message');

            memoInput.value = existingMemo || '';
            nameInput.value = existingName || '';
            messageInput.value = existingMessage || '';

            dialog.showModal();

            document.getElementById('saveAppointment').onclick = function() {
                var memo = memoInput.value;
                var name = nameInput.value;
                var message = messageInput.value;

                if (memo || name || message) {
                    selectedDay.setAttribute('data-memo', memo);
                    selectedDay.setAttribute('data-name', name);
                    selectedDay.setAttribute('data-message', message);
                    selectedDay.classList.add('has-appointment');
                    selectedDay.classList.remove('no-appointment');
                    noteDiv.innerHTML = `memo: ${memo}<br>名前: ${name}<br>メッセージ: ${message}`;
                    noteDiv.style.display = 'none';
                } else {
                    selectedDay.removeAttribute('data-memo');
                    selectedDay.removeAttribute('data-name');
                    selectedDay.removeAttribute('data-message');
                    selectedDay.classList.add('no-appointment');
                    selectedDay.classList.remove('has-appointment');
                    noteDiv.innerHTML = '';
                }
                dialog.close();
            };

            document.getElementById('dialog-close-btn').onclick = function() {
                dialog.close();
            };
        });

        dayElement.classList.add('no-appointment');
    }

    // Get today's date
    var today = new Date();
    var currentDay = today.getDate();

    // Get today's month and year
    currentMonth = today.getMonth() + 1;
    currentYear = today.getFullYear();

    // Function to update the month name and year
    function updateMonthNameAndYear(month, year) {
        var monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
        var monthName = monthNames[month - 1];
        document.querySelector('.month ul li:nth-child(3)').innerHTML = monthName + '<br><span style="font-size:18px">' + year + '</span>';
    }

    // Find the li element for the current day and add the active class to the span inside it
    var currentDayElement = document.querySelector('.days li[data-day="' + currentDay + '"] span');
    if (currentDayElement) {
        currentDayElement.classList.add('active');
    }

    // Call the function to update the calendar days with the current month and year
    updateCalendarDays(currentYear, currentMonth);
    updateMonthNameAndYear(currentMonth, currentYear);

    // Previous month button click event
    document.querySelector('.prev').addEventListener('click', function() {
        currentMonth--;
        if (currentMonth === 0) {
            currentMonth = 12;
            currentYear--;
        }
        // Update the month name and year
        updateMonthNameAndYear(currentMonth, currentYear);
        // Update the calendar days for the new month and year
        updateCalendarDays(currentYear, currentMonth);
    });

    // Next month button click event
    document.querySelector('.next').addEventListener('click', function() {
        currentMonth++;
        if (currentMonth === 13) {
            currentMonth = 1;
            currentYear++;
        }
        // Update the month name and year
        updateMonthNameAndYear(currentMonth, currentYear);
        // Update the calendar days for the new month and year
        updateCalendarDays(currentYear, currentMonth);
    });

    // Your existing code for handling day clicks goes here

    // Your existing code for styling and functionality of the dialog goes here
});