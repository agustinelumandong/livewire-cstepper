# Contributing to Livewire CStepper

Contributions are **welcome** and will be fully **credited**.

Thank you for considering contributing to Livewire CStepper! This guide will help you understand our development process and requirements.

Please read and understand the contribution guide before creating an issue or pull request.

## Etiquette

This project is open source, and as such, the maintainers give their free time to build and maintain the source code
held within. They make the code freely available in the hope that it will be of use to other developers. It would be
extremely unfair for them to suffer abuse or anger for their hard work.

Please be considerate towards maintainers when raising issues or presenting pull requests. Let's show the
world that developers are civilized and selfless people.

It's the duty of the maintainer to ensure that all submissions to the project are of sufficient
quality to benefit the project. Many developers have different skillsets, strengths, and weaknesses. Respect the maintainer's decision, and do not be upset or abusive if your submission is not used.

## Viability

When requesting or submitting new features, first consider whether it might be useful to others. Open
source projects are used by many developers, who may have entirely different needs to your own. Think about
whether or not your feature is likely to be used by other users of the project.

For Livewire CStepper specifically, consider:

- Does the feature enhance multi-step form functionality?
- Is it compatible with Laravel and Livewire 3?
- Does it maintain the package's focus on clean, modern UI with WireUI integration?
- Will it benefit the broader Laravel community?

## Procedure

Before filing an issue:

- Attempt to replicate the problem, to ensure that it wasn't a coincidental incident.
- Check to make sure your feature suggestion isn't already present within the project.
- Check the pull requests tab to ensure that the bug doesn't have a fix in progress.
- Check the pull requests tab to ensure that the feature isn't already in progress.

Before submitting a pull request:

- Check the codebase to ensure that your feature doesn't already exist.
- Check the pull requests to ensure that another person hasn't already submitted the feature or fix.
- Make sure you have tested your changes with Laravel and Livewire 3.
- Ensure your changes work with WireUI components if applicable.
- Verify that your code follows the project's architectural patterns.

## Development Setup

To contribute to Livewire CStepper, you'll need:

- PHP 8.1 or higher
- Composer
- Laravel 10.x or 11.x knowledge
- Livewire 3.x experience
- Basic understanding of WireUI components

### Local Development

1. Clone the repository
2. Run `composer install`
3. Set up the test environment with `composer test`
4. Make your changes
5. Run tests to ensure everything works

## Requirements

All contributions must meet these requirements:

- **[PSR-12 Coding Standard](https://www.php-fig.org/psr/psr-12/)** - Use [PHP CS Fixer](https://cs.symfony.com/) or [PHP Code Sniffer](https://github.com/squizlabs/PHP_CodeSniffer) to ensure compliance.

- **Add tests!** - Your patch won't be accepted if it doesn't have tests. We use PHPUnit and Orchestra Testbench for testing Laravel packages.

- **Test with Livewire 3** - Ensure your changes work correctly with Livewire 3 components and lifecycle methods.

- **WireUI Compatibility** - If your changes affect UI components, test with WireUI to maintain visual consistency.

- **Document any change in behaviour** - Update the `README.md`, inline documentation, and any other relevant documentation.

- **Follow naming conventions** - Use our unique naming conventions (CStepper, StepComponent, etc.) to maintain consistency.

- **Consider our release cycle** - We follow [SemVer v2.0.0](https://semver.org/). Breaking changes require major version bumps.

- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please [squash them](https://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages) before submitting.

## Testing Guidelines

- Write unit tests for all new functionality
- Include feature tests for complex workflows
- Test step navigation and validation scenarios
- Verify state management and serialization
- Test WireUI component integration

## Code Style

- Use descriptive variable and method names
- Follow the existing architectural patterns
- Add PHPDoc comments for public methods
- Keep methods focused and single-purpose
- Use type hints and return types where appropriate

## Package-Specific Guidelines

### Architecture Principles

Livewire CStepper follows specific architectural principles:

- **Clean-room implementation** - We maintain our own unique codebase and naming conventions
- **Livewire 3 first** - Built specifically for Livewire 3 with modern practices
- **WireUI integration** - Seamless integration with WireUI components for consistent styling
- **State management** - Proper serializable state handling for multi-step forms
- **Navigation guards** - Validation-based step navigation with proper error handling

### Component Development

When developing new components:

- Extend existing base classes (`CStepper`, `StepComponent`)
- Use the provided traits (`ManagesFormData`, `HandlesNavigation`, etc.)
- Follow the established lifecycle patterns
- Maintain compatibility with the configuration system
- Ensure proper error handling and user feedback

### Documentation

- Update relevant examples in the `examples/` directory
- Add inline code documentation
- Include usage examples for new features
- Update configuration documentation if needed

## Getting Help

- Check existing issues and discussions
- Review the implementation summary and architecture guide
- Look at the examples directory for usage patterns
- Ask questions in GitHub Discussions before opening issues

**Happy coding**!
