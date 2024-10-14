<div class="preloader">
    <div class="loader">
        <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt="Logo Preloader" class="logo-loader">
    </div>
</div>

    <style>
        /* Preloader container */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.8); /* Semi-transparent background */

            /* Or any background color you prefer */
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        /* Loader animation */
        .logo-loader {
            width: 100px;
            /* Adjust size as necessary */
            animation: spin 3s linear infinite;
            /* Or use pulse animation */
            filter: drop-shadow(0 0 10px rgba(0, 0, 0, 0.1));
            /* Optional shadow */
        }

        /* Spinning animation */
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Optional pulse effect */
        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }
    </style>
