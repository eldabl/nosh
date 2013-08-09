phar:
	bin/build_phar.php
	chmod +x nosh.phar

package: phar
	pear package

install: phar
	cp nosh.phar /usr/local/bin/nosh.phar