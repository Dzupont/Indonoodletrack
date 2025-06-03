// Load dashboard statistics
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardStats();
    loadRecentActivity();
});

function loadDashboardStats() {
    fetch('api/dashboard-stats.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalStock').textContent = data.totalStock;
            document.getElementById('pendingRequests').textContent = data.pendingRequests;
            document.getElementById('activeUsers').textContent = data.activeUsers;
        })
        .catch(error => console.error('Error loading stats:', error));
}

function loadRecentActivity() {
    fetch('api/recent-activity.php')
        .then(response => response.json())
        .then(data => {
            const activityList = document.getElementById('activityList');
            activityList.innerHTML = data.map(activity => `
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas ${activity.iconClass}"></i>
                    </div>
                    <div class="activity-details">
                        <p>${activity.message}</p>
                        <small>${activity.time}</small>
                    </div>
                </div>
            `).join('');
        })
        .catch(error => console.error('Error loading activity:', error));
}

// Add smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});
