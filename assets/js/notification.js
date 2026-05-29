/**
 * Simple Notification System for Lead Management
 * Checks for due followups every 30 seconds
 */

function NotificationSystem() {
    this.lastCount = 0;
    this.intervalId = null;
    this.init();
}

NotificationSystem.prototype.init = function() {
    this.requestPermission();
    this.startPolling();
}

NotificationSystem.prototype.requestPermission = function() {
    if ("Notification" in window) {
        if (Notification.permission === "granted") {
            this.permissionGranted = true;
        }
    }
}

NotificationSystem.prototype.startPolling = function() {
    if (this.intervalId) {
        clearInterval(this.intervalId);
    }
    var self = this;
    this.intervalId = setInterval(function() {
        self.checkNotifications();
    }, 30000);
}

NotificationSystem.prototype.checkNotifications = function() {
    var self = this;
    var now = new Date();
    var year = now.getFullYear();
    var month = ("0" + (now.getMonth() + 1)).slice(-2);
    var day = ("0" + now.getDate()).slice(-2);
    var hours = ("0" + now.getHours()).slice(-2);
    var minutes = ("0" + now.getMinutes()).slice(-2);
    var seconds = ("0" + now.getSeconds()).slice(-2);
    var currentDateTime = year + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;
    
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../config/fornotification.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            self.processResponse(xhr.responseText);
        }
    };
    xhr.send("CurrentDateAndTime=" + encodeURIComponent(currentDateTime));
}

NotificationSystem.prototype.processResponse = function(html) {
    var self = this;
    var parser = new DOMParser();
    var doc = parser.parseFromString(html, "text/html");
    
    var countDiv = doc.getElementById("notify-count");
    if (countDiv) {
        var count = parseInt(countDiv.textContent) || 0;
        this.updateCount(count);
        
        if (count > this.lastCount && this.lastCount > 0) {
            this.showToast(count);
        }
        
        var container = document.getElementById("notificationContainer");
        if (container) {
            container.innerHTML = html;
        }
        
        this.lastCount = count;
    }
}

NotificationSystem.prototype.updateCount = function(count) {
    var countElement = document.getElementById("notificationCount");
    var badgeElement = document.getElementById("notificationBadge");
    
    if (countElement) {
        if (count > 0) {
            countElement.textContent = (count > 50) ? "50+" : count;
            if (badgeElement) {
                badgeElement.style.display = "block";
            }
        } else {
            countElement.textContent = "";
            if (badgeElement) {
                badgeElement.style.display = "none";
            }
        }
    }
}

NotificationSystem.prototype.showToast = function(count) {
    var existingToast = document.getElementById("followup-toast");
    if (existingToast) {
        existingToast.remove();
    }
    
    var toast = document.createElement("div");
    toast.id = "followup-toast";
    toast.style.cssText = "position:fixed;top:20px;right:20px;width:350px;background:white;border:1px solid #ddd;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:9999;";
    
    toast.innerHTML = '<div style="display:flex;align-items:center;padding:10px 15px;background:#f8f9fa;border-bottom:1px solid #ddd;border-radius:8px 8px 0 0;">' +
        '<img src="../assets/images/others/logo-dark.png" width="20" height="20" style="margin-right:8px;">' +
        '<strong style="flex:1;">Followup Reminder</strong>' +
        '<small>Just now</small>' +
        '</div>' +
        '<div style="padding:15px;">' +
        '<strong>You have ' + count + ' followup' + ((count > 1) ? 's' : '') + ' due!</strong>' +
        '<div style="margin-top:10px;">' +
        '<a href="followup-todays-list.php" class="btn btn-sm btn-primary">View Followups</a>' +
        '</div>' +
        '</div>';
    
    document.body.appendChild(toast);
    
    setTimeout(function() {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 10000);
}

document.addEventListener("DOMContentLoaded", function() {
    window.notificationSystem = new NotificationSystem();
});
