const express = require('express');
const app = express();

// ... other middleware and routes ...

const PORT = 5000;
app.listen(PORT, () => {
    console.log(`API Server running on port ${PORT}`);
}); 