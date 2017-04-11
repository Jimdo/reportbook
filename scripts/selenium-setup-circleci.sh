# Install Selenium
curl http://selenium-release.storage.googleapis.com/2.53/selenium-server-standalone-2.53.1.jar > selenium-server-standalone.jar

# Install Google Chrome
curl http://chromedriver.storage.googleapis.com/2.23/chromedriver_linux64.zip | gzip -dc > chromedriver
chmod +x chromedriver

# Install Firefox
curl https://github.com/mozilla/geckodriver/releases/download/v0.15.0/geckodriver-v0.15.0-linux64.tar.gz > firefoxdriver
chmod +x firefoxdriver