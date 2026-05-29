<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 text-center">
                <a href="https://www.kesyainternational.com/" class="fw-bold footer-text" target="_blank"> Kesya International Pvt. Ltd.</a>
            </div>
        </div>
    </div>
</footer>

<script>
    // Loader
    window.addEventListener("load", function() {
        document.getElementById("loader-wrapper").style.display = "none";
        document.getElementById("wrapper").style.display = "block";
    });
</script>


<script>
    // Date and Time
    function updateTime() {
        const currentTime = new Date();
        const hours = currentTime.getHours();
        const minutes = currentTime.getMinutes();
        const seconds = currentTime.getSeconds();
        const ampm = hours >= 12 ? 'PM' : 'AM';
        const formattedTime = `${hours % 12 || 12}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')} ${ampm}`;
        document.getElementById('time').innerHTML = `${formattedTime}`;
    }
    setInterval(updateTime, 1000); // update every 1000 milliseconds (1 second)
</script>


<script>
    // realtime for notification fetching
    function updateDateTime() {
        const now = new Date();
        const formatted = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')} ${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}:${String(now.getSeconds()).padStart(2, '0')}`;

        fetch("#", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `CurrentDateAndTime=${encodeURIComponent(formatted)}`
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById("notificationContainer").innerHTML = data;

                // Extract and show count from hidden div
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data;
                const countDiv = tempDiv.querySelector('#notify-count');
                const count = countDiv ? countDiv.textContent.trim() : '0';

                document.getElementById("notificationCount").innerText = count;
            })
            .catch(error => console.error('Error:', error));
    }

    updateDateTime();
    setInterval(updateDateTime, 1000);
</script>