
@set /p entityPath= Enter entity's name: 
php app/console doctrine:generate:entities EPBOutage/MainBundle/Entity/%entityPath%
@PAUSE