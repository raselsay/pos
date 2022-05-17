try {
    window.MakePdf=require('pdfmake');
    window.HtmlToPdfMake=require('html-to-pdfmake');
    var fonts=require('pdfmake/build/vfs_fonts.js');
} catch (e) {}

    MakePdf.vfs = fonts.pdfMake.vfs;