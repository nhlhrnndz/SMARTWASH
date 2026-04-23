<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sample Interface</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f4f4f4;
        }

        header {
            background: #333;
            color: white;
            padding: 15px 20px;
            text-align: center;
        }

        nav {
            background: #555;
            display: flex;
            justify-content: center;
        }

        nav a {
            color: white;
            padding: 14px 20px;
            text-decoration: none;
        }

        nav a:hover {
            background: #777;
        }

        .hero {
            background: #007BFF;
            color: white;
            padding: 60px 20px;
            text-align: center;
        }

        .container {
            padding: 20px;
        }

        .cards {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .card {
            background: white;
            padding: 20px;
            flex: 1 1 250px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .card h3 {
            margin-bottom: 10px;
        }

        .btn {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            background: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn:hover {
            background: #0056b3;
        }

        footer {
            text-align: center;
            padding: 15px;
            background: #333;
            color: white;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<header>
    <h1>My Website</h1>
</header>

<nav>
    <a href="#">Home</a>
    <a href="#">About</a>
    <a href="#">Services</a>
    <a href="#">Contact</a>
</nav>

<section class="hero">
    <h2>Welcome to My Website</h2>
    <p>This is a simple web interface example.</p>
    <a href="#" class="btn">Get Started</a>
</section>

<div class="container">
    <h2>Our Features</h2>
    <div class="cards">
        <div class="card">
            <h3>Feature 1</h3>
            <p>Short description of this feature.</p>
            <a href="#" class="btn">Learn More</a>
        </div>

        <div class="card">
            <h3>Feature 2</h3>
            <p>Short description of this feature.</p>
            <a href="#" class="btn">Learn More</a>
        </div>

        <div class="card">
            <h3>Feature 3</h3>
            <p>Short description of this feature.</p>
            <a href="#" class="btn">Learn More</a>
        </div>
    </div>
</div>

<footer>
    <p>© 2026 My Website</p>
</footer>

</body>
</html>