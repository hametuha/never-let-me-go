# Contributing Guide

## Project Overview

This repository is dedicated to the development of the WordPress plugin [Never Let Me Go](https://wordpress.org/plugins/never-let-me-go/).
If you're interested in contributing, please follow the steps outlined below.

## License

Since this project is a derivative of WordPress, it is distributed under the GPL license.
For more details, please refer to the [LICENSE file](LICENSE).

## How to Contribute

There are several ways to contribute to this project. Below are a few methods.

### Reporting Issues

If you find a bug or have a feature request, please [create a new issue](https://github.com/hametuha/never-let-me-go/issues/new/choose) to report it.
When reporting, please provide as much detail as possible.

You are also welcome to repost issues from the [support forum](https://wordpress.org/support/plugin/never-let-me-go/). In such cases, please make sure to include a link to the support forum post in the issue description.

### Pull Requests

When submitting a pull request, please follow these steps:

1. Fork the project.
2. Create a new branch (`git checkout -b feature/your-feature`).
3. Make your changes and commit them (`git commit -m 'Add some feature'`).
4. Push the branch to your repository (`git push origin feature/your-feature`).
5. Create a pull request. In the pull request, please include the relevant issue number (e.g. #1) and details of the changes made.

### Tests and Code Style

Pull requests will only be merged if they pass the following tests:

- PHPUnit
- PHP CodeSniffer
- eslint
- stylelint

For coding standards, we follow the [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/).

## Local Development

To create a pull request, please follow the steps below.

### Required Tools

- PHP and composer (required versions are listed in composer.json)
- node and npm (required versions are listed in package.json)
- Docker (for setting up a local development environment)

### Setup

Fork the repository, clone it, and start working in the directory.

```bash
# Install composer dependencies
composer install
# Install npm packages
npm install
# Build CSS/JS
npm run package
# Start Docker
npm start
# Now you're ready to start development
```

Since this plugin includes membership features, there are many processes related to email.
You can add MailHog for local email testing.

```bash
# Check Docker's install path while Docker is running
npm run path
> /Users/your-name/.wp-env/178c3c8877e45f1b496b2353e34d7f96
# Save this last hash value as .wp_install_path
echo 178c3c8877e45f1b496b2353e34d7f96 > .wp_install_path
# Restart Docker including MailHog by stopping and starting again.
npm run env stop
npm start
# Access http://localhost:8025 to verify MailHog is running successfully.
```

### CI/CD

The following tools are provided to assist with local development:

```bash
# Automatically rebuild on file changes
npm run watch
# Run PHPUnit in Docker
npm test
# Lint PHP code
composer lint
# Automatically fix PHP syntax issues
composer fix
```

### Acknowledgements

Thank you for contributing to this project! Your support is crucial to the growth of this project.
If your name is not listed as a contributor, feel free to reach out anytime.
