# Contributing Guidelines

We welcome contributions to **LENTERA Score**! Below are some guidelines to help you get started.

## How to Contribute
1. **Fork the repository** and clone your fork.
2. **Create a branch** for your feature or bug fix:
   ```bash
   git checkout -b my-feature
   ```
3. **Make your changes** following the existing coding style (Laravel, Inertia, Tailwind, PHP 8+, JavaScript ES2022).
4. **Write tests** for new functionality when applicable.
5. **Run the test suite** to ensure everything passes:
   ```bash
   php artisan test
   ```
6. **Commit your changes** with a clear commit message.
7. **Push** to your fork and open a **Pull Request** against the `main` branch.

## Code Style
- PHP: PSR‑12 coding style.
- JavaScript/TypeScript: follow the project's ESLint configuration.
- CSS: Tailwind utilities – avoid custom CSS when possible.
- Use the existing design system tokens for colors, spacing, and typography.

## Review Process
- PRs will be reviewed by maintainers for correctness, security, and adherence to the design system.
- Small, focused changes are preferred (YAGNI principle).
- Include a short description of what the PR does and any related issue numbers.

## Reporting Issues
- Use the GitHub Issues tracker.
- Include steps to reproduce, expected behavior, and environment details.

## Thank You!
Your help makes this project better for everyone.
