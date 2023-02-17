### Software

Install php-gd (Required by php library to scan QR code)
Install pdftk (For extracting FDF form data from pdf)
poppler-utils (For pdfimages tool to extract images from pdf)
Install qpdf to decrypt pdf (pdftk does not support AES256 encrypted PDFs https://gitlab.com/pdftk-java/pdftk/-/issues/87)

```
sudo apt-get install php-gd
sudo apt-get install pdftk
sudo apt-get install poppler-utils
```


### For OCR

```
sudo apt-get install tesseract-ocr
sudo apt-get install tesseract-ocr-chi-tra
```
