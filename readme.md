### Software

Install php-gd (Required by php library to scan QR code)
Install pdftk (For extracting FDF form data from pdf)
poppler-utils (For pdfimages tool to extract images from pdf)
Install qpdf to decrypt pdf (pdftk does not support AES256 encrypted PDFs https://gitlab.com/pdftk-java/pdftk/-/issues/87)

```
sudo apt install php-gd
sudo apt install pdftk
sudo apt install poppler-utils
```
