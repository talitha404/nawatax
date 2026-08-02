<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About NawaTax

NawaTax is a web-based **Shipbroker Profit & Tax Calculator** designed to provide accurate and real-time calculations for the shipbroking industry. Built with Laravel, Blade, and Alpine.js, this application helps shipbrokers precisely determine:

-   **Brokerage fees**
-   **Transaction taxes** (PPh 23, PPh 15, PPh 26) based on vessel status
-   **VAT (PPN)**
-   **Commission splits** (including sub-broker taxation mechanisms)
-   **Operational costs**
-   **Final tax implications**
-   **Net profit**

### Key Features:

-   **Comprehensive Calculator**: Input transaction values, tax profiles, and commission split schemes to get a detailed cash flow summary, tax breakdown, and profitability report.
-   **Dynamic Tax Handling**: Supports various Indonesian tax regulations (PPh 15 for National Shipping, PPh 23 for non-SIUPAL, PPh 15 WPLN for Foreign Shipping with BUT, PPh 26 for Non-Resident) and PPN calculations based on PKP status.
-   **Inter-Broker Split Management**: Accurately calculates and reports commission splits with sub-brokers, including tax deductions (PPh 23 for entities, PPh 21 for individuals) without burdening the main broker.
-   **Real-time Results**: All calculations are performed instantly as users adjust parameters, providing immediate insights.
-   **Document Generator**: Export detailed calculation results into clean, professional PDF reports for record-keeping or client presentation.
-   **Guest-Friendly**: No login is required, ensuring quick and easy access for all users.

NawaTax aims to simplify complex shipbroking financial calculations, offering transparency and accuracy in profit and tax reporting.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
