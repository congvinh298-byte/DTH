const fs = require('fs');
const path = require('path');

const dir = 'C:\\Users\\pcpv\\OneDrive\\Desktop\\DTH';
const extensions = ['.php', '.js', '.css', '.html', '.sql'];
const excludes = ['node_modules', '.git', 'dth_full.zip', 'vendor'];

function walkDir(currentDir) {
    fs.readdir(currentDir, (err, files) => {
        if (err) return console.error(err);
        
        files.forEach(file => {
            const filePath = path.join(currentDir, file);
            
            fs.stat(filePath, (err, stats) => {
                if (err) return console.error(err);
                
                if (stats.isDirectory()) {
                    if (!excludes.includes(file)) {
                        walkDir(filePath);
                    }
                } else if (stats.isFile()) {
                    const ext = path.extname(file).toLowerCase();
                    if (extensions.includes(ext) && !excludes.includes(file)) {
                        processFile(filePath);
                    }
                }
            });
        });
    });
}

function processFile(filePath) {
    fs.readFile(filePath, 'utf8', (err, data) => {
        if (err) return;
        
        let original = data;
        let content = data;
        
        content = content.replace(/DMH\.VN/g, 'dienmayhieu.com');
        content = content.replace(/DMH\.COM/g, 'dienmayhieu.com');
        content = content.replace(/Điện Máy Hiếu/g, 'Điện Máy Hiếu');
        content = content.replace(/Điện Máy Hiếu/g, 'Điện Máy Hiếu');
        content = content.replace(/DMH/g, 'DMH');
        content = content.replace(/dmh/g, 'dmh');
        content = content.replace(/\/\*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI DMH - ZALO: 0812665001\*\//g, '');
        content = content.replace(/DMH/g, 'DMH');
        
        if (content !== original) {
            fs.writeFile(filePath, content, 'utf8', err => {
                if (err) console.error(err);
                else console.log(`Modified: ${filePath}`);
            });
        }
    });
}

walkDir(dir);
