.PHONY: test coverage coverage-for-infection infection-test infection

test:
	./vendor/bin/phpunit

coverage:
	php -dzend_extension=xdebug.so ./vendor/bin/phpunit --coverage-html=coverage/coverage-html

coverage-for-infection:
	php -dzend_extension=xdebug.so ./vendor/bin/phpunit --coverage-xml=coverage/coverage-xml --log-junit=coverage/phpunit.junit.xml

infection-test:
	./vendor/bin/infection --threads=4 --coverage=coverage

infection:
	$(MAKE) coverage-for-infection && $(MAKE) infection-test
