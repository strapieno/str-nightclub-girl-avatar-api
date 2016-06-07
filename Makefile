COMPOSER_DEV = 

# start download pre setup
install:
	docker pull composer/composer

# star setup platform
composer:
	docker run --rm -v $(PWD):/app composer/composer update --ignore-platform-reqs --prefer-dist -o $(COMPOSER_DEV)
