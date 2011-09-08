# Automake file for sdk
# $Id: $
#

sdkdir = ${prefix}/sdk
phpdir = ${sdkdir}/php
phpappdir = ${phpdir}/app
phplibdir = ${phpdir}/lib

phpapp_DATA = php/app/demo.ini
phplib_DATA = php/lib/XS.php

EXTRA_DIST = php/README php/doc php/app/demo.ini php/lib/XS.php php/util

php/lib/XS.php:
	@echo "Regenerating XS library class file ..."
	@php php/dev/build.php lite > /dev/null 2>&1

dist-hook: ../etc/py.xdb
	@echo "Regenerating offline API reference ..."
	@rm -rf php/doc/api	
	@php php/dev/build.php api offline > /dev/null 2>&1
	@echo "Regenerating XS library class file ..."
	@php php/dev/build.php lite > /dev/null 2>&1	

../etc/py.xdb:
	@echo "Generating pinyin dict by php ..."
	@php php/dev/build.php pinyin make > /dev/null 2>&1
	
php-web-doc:
	@echo "Regenerating online API reference ..."
	@rm -rf php/doc/api
	@php php/dev/build.php api online > /dev/null 2>&1
	@echo "Making tar ball ..."
	@tar -cjf ../xunsearch-web-doc-latest.tar.bz2 -C php doc/api doc/guide
	@echo "Regenerating offline API reference ..."
	@rm -rf php/doc/api	
	@php php/dev/build.php api offline > /dev/null 2>&1	

install: install-am install-sdk
	
uninstall: uninstall-am uninstall-sdk

install-sdk:
	$(MKDIR_P) "$(DESTDIR)$(phpdir)"
	-cp -rf php/README php/util php/doc $(DESTDIR)$(phpdir)/
	chmod 0755 $(DESTDIR)$(phpdir)/util/*.php

uninstall-sdk:
	-rmdir $(phpappdir) $(phplibdir)
	-rm -rf $(phpdir)/README $(phpdir)/util $(phpdir)/doc
	-rmdir $(phpdir) $(sdkdir)