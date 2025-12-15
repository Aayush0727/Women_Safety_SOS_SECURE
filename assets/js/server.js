const express = require("express");
const app = express();
app.use(express.json());

let policeStations = [];

// Add Police
app.post("/add-police", (req, res) => {
    const { name, contact } = req.body;
    policeStations.push({ id: Date.now(), name, contact });
    res.status(201).json({ message: "Police added." });
});

// Get Police Contacts
app.get("/get-police", (req, res) => res.json(policeStations));

// Remove Police
app.delete("/remove-police/:id", (req, res) => {
    policeStations = policeStations.filter(police => police.id != req.params.id);
    res.json({ message: "Police removed." });
});

// Get Nearest Police
app.get("/get-nearest-police", (req, res) => {
    if (policeStations.length > 0) {
        res.json(policeStations[0]); // Simplified logic
    } else {
        res.json({});
    }
});

app.listen(5000, () => console.log("Server running on port 5000"));
