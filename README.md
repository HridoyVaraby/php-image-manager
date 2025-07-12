# Simple Image Host

Simple Image Host is a straightforward PHP-based application designed for uploading, storing, and managing images. It provides an easy way to host images on any cPanel-based hosting and use the image URLs in other projects, such as those built with React, Vite, and Supabase.

## Features

- **Image Upload:** Easily upload images through a simple web interface.
- **Image Gallery:** View all uploaded images in a clean, organized gallery.
- **Copy URL:** Quickly copy the direct URL of any image with a single click.
- **File Management:** Rename and delete images directly from the gallery.
- **Secure:** Includes CSRF protection and validation for file uploads.

## Use Case

This project is ideal for developers who need a simple, self-hosted solution for managing images. For example, if you are building a web application with a modern frontend framework (like React or Vue) and a backend service like Supabase, you can use Simple Image Host to handle image hosting on a separate, low-cost cPanel server. This keeps your main project clean and separates concerns.

## Getting Started

### Prerequisites

- A web server with PHP support (e.g., Apache, Nginx).
- PHP enabled on your server.

### Installation

1.  **Clone or download the repository:**

    ```bash
    git clone [your-repository-url]
    ```

2.  **Navigate to the project directory:**

    ```bash
    cd simple-image-host
    ```

3.  **Configure the application:**

    - Rename `config.php.example` to `config.php`.
    - Open `config.php` and set your desired username and password:

      ```php
      define('USERNAME', 'your_username');
      define('PASSWORD', 'your_password');
      ```

4.  **Set permissions:**

    Ensure that the `uploads/` directory is writable by the web server.

    ```bash
    chmod 755 uploads
    ```

5.  **Access the application:**

    Open your web browser and navigate to the URL where you have hosted the files.

## How It Works

- **`index.php`:** The main page that displays the image gallery and the upload form.
- **`upload.php`:** Handles the file upload logic, including validation and moving the file to the `uploads/` directory.
- **`rename.php`:** Manages renaming files.
- **`delete.php`:** Handles file deletion.
- **`config.php`:** Contains all the configuration settings, including credentials and file upload limits.
- **`login.php` / `logout.php`:** Manages user authentication.

## Contributing

Contributions are welcome! If you have any ideas for improvements or find any issues, feel free to open an issue or submit a pull request.

## License

This project is open-source and available under the [MIT License](LICENSE).