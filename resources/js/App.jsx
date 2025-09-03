import React from "react";
import { createRoot } from "react-dom/client";
import App from "./src/App.jsx";
import "../css/app.css";

const rootElement = document.getElementById("root");
if (rootElement) {
    createRoot(rootElement).render(<App />);
}
