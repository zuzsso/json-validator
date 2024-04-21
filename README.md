# ComposerTemplateProject

Use this template if you want to create a PHP library. 

Clone this project, rename and push the changes to your new repository.

It features a minimalistic bootstrap file to run tests, which has access to the DI container. See examples of this in `tests/HelloWorld/HelloWorldTest.php` 

# Placeholders (I)
Change the following properties in `composer.json`:
1. `name` 
2. `authors`
3. `autoload.psr-4.YourProject\\`
4. `autoload-dev.ps4-4.YourProject\\Tests\\`

# Placeholders (II)
Change `AbstractDependencyInjection` namespace to match the PSR-4 namespace structure or your project

# Placeholders (III)
Change `DependencyInjectionManifest` namespace to match the PSR-4 namespace structure of your project

# Placeholders (IV)
Change `CustomTestCase` namespace to match the PSR-4 namespace

# Built-in scripts (I)
`build.sh` will install vendors and dump the autoloader.

# Built-in scripts (II)
`bash_scripts/project_maintenance/run_php_cs_fixer.sh` Will apply PSR-12 coding standards to all PHP files in the `src` and `tests`directories. 

Paths are relative to that script, so you should run it from its directory, i.e.:

```
$ cd <YOUR_PROJECT_ROOT>/bash_scripts/project_maintenance
$ ./run_php_cs_fixer
