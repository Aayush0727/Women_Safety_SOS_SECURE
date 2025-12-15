document.addEventListener("DOMContentLoaded", function () {
    loadProfile(); // Load user profile immediately
});

// Function to load profile info from localStorage
function loadProfile() {
    const user = JSON.parse(localStorage.getItem("registeredUser"));
    if (user) {
        document.getElementById("profileName").textContent = user.name || "Admin";
        document.getElementById("profileEmail").textContent = user.email || "admin@example.com";
        document.getElementById("profileDropdownName").textContent = user.name || "Admin";
        document.getElementById("profileDropdownEmail").textContent = user.email || "admin@example.com";
    }
}

// Function to logout
function logout() {
    if (confirm("Are you sure you want to logout?")) {
        localStorage.removeItem("registeredUser");
        window.location.href = "login.html";
    }
}




// Flags to ensure data is loaded only once per section
let isUsersSectionLoaded = false;
let isSOSSectionLoaded = false;
let isPoliceSectionLoaded = false;

// Function to show sections dynamically
function showSection(sectionId) {
    let sections = document.querySelectorAll("section");
    sections.forEach(section => {
        if (section.id === sectionId) {
            section.classList.remove("hidden"); // Show the clicked section
        } else {
            section.classList.add("hidden"); // Hide others
        }
    });

    // Load data only once per section
    if (sectionId === "usersSection" && !isUsersSectionLoaded) {
        loadRegisteredUsers();
        isUsersSectionLoaded = true;
    } else if (sectionId === "sosAlertsSection" && !isSOSSectionLoaded) {
        loadSOSAlerts();
        isSOSSectionLoaded = true;
    } else if (sectionId === "policeSection" && !isPoliceSectionLoaded) {
        loadRegisteredPolice();
        isPoliceSectionLoaded = true;
    }
}

// Function to populate Registered Users table
function loadRegisteredUsers() {
    let usersTable = document.getElementById("usersTable");

    let usersData = [
        { userName: "Alice Johnson", guardianName: "John Johnson", guardianEmail: "john@example.com", guardianContact: "9876543210" },
        { userName: "Bob Smith", guardianName: "Emma Smith", guardianEmail: "emma@example.com", guardianContact: "9123456789" },
        { userName: "Charlie Brown", guardianName: "Sarah Brown", guardianEmail: "sarah@example.com", guardianContact: "9988776655" }
    ];

    usersTable.innerHTML = ""; // Clear existing content

    usersData.forEach(user => {
        let row = `<tr>
            <td>${user.userName}</td>
            <td>${user.guardianName}</td>
            <td>${user.guardianEmail}</td>
            <td>${user.guardianContact}</td>
        </tr>`;
        usersTable.innerHTML += row;
    });
}

// Function to populate SOS Alerts table
function loadSOSAlerts() {
    let sosTable = document.getElementById("sosTable");

    let sosData = [
        { user: "Alice Johnson", location: "https://goo.gl/maps/example1", time: "2025-03-01 14:30" },
        { user: "Bob Smith", location: "https://goo.gl/maps/example2", time: "2025-03-01 15:10" },
        { user: "Charlie Brown", location: "https://goo.gl/maps/example3", time: "2025-03-01 16:45" }
    ];

    sosTable.innerHTML = ""; // Clear existing content

    sosData.forEach(alert => {
        let row = `<tr>
            <td>${alert.user}</td>
            <td><a href="${alert.location}" target="_blank">View Location</a></td>
            <td>${alert.time}</td>
        </tr>`;
        sosTable.innerHTML += row;
    });
}

// Function to populate Registered Police table
function loadRegisteredPolice() {
    let policeTable = document.getElementById("policeTable");

    let policeData = [
        { officerName: "Officer Mark", station: "Central PD", email: "mark@police.com", contact: "9876543211" },
        { officerName: "Officer Lisa", station: "Westside PD", email: "lisa@police.com", contact: "9123456790" },
        { officerName: "Officer Steve", station: "Downtown PD", email: "steve@police.com", contact: "9988776656" }
    ];

    policeTable.innerHTML = ""; // Clear existing content

    policeData.forEach(police => {
        let row = `<tr>
            <td>${police.officerName}</td>
            <td>${police.station}</td>
            <td>${police.email}</td>
            <td>${police.contact}</td>
        </tr>`;
        policeTable.innerHTML += row;
    });
}
