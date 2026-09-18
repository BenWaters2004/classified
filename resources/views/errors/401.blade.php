<!doctype html>
<html lang="en">
<head>
    <title>401 Unauthorised</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background-color: #2c3c64;
            color: white;
            text-align: center;
            padding: 20px;
        }

        a {
            text-decoration: none;
            color: #ffffff;
        }

        /* Logo Styling */
        .logo {
            display: block;
            max-width: 250px;
            margin-bottom: 20px;
        }

        /* Error Box Styling */
        .errorPageBox {
            background-color: #C55359;
            color: white;
            border: 5px solid white;
            border-radius: 10px;
            padding: 20px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .errorPageBox h1 {
            font-size: 5rem;
            margin-bottom: 10px;
        }

        .errorPageBox p {
            font-size: 1.2rem;
            margin-bottom: 20px;
        }

        .errorPageBox a {
            display: inline-block;
            padding: 10px 20px;
            background-color: white;
            color: #C55359;
            font-weight: bold;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .errorPageBox a:hover {
            background-color: #C55359;
            color: white;
            border: 2px solid white;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .errorPageBox h1 {
                font-size: 3.5rem;
            }

            .errorPageBox p {
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .errorPageBox h1 {
                font-size: 3rem;
            }

            .errorPageBox p {
                font-size: 0.9rem;
            }

            .errorPageBox a {
                padding: 8px 16px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <!-- Logo -->
    <a href="{{ env('APP_URL') }}" class="logo">
        <img class="logo" src="{{ env('APP_URL') }}images/whiteWithStrap.png" alt="ClassifIeD Logo"/>
    </a>

    <!-- Error Page Content -->
    <div class="errorPageBox">
        <h1>401</h1>
        <p>Sorry, you need to be logged in to view this page.</p>
        <a href="{{ env('APP_URL') }}login">Go back</a>
    </div>
</body>
</html>
