const fs = require('fs');
const path = require('path');

const dir = 'c:/laragon/www/app-ngodingpintar';
const directoriesToSearch = ['resources/js', 'resources/views', 'app', 'config', 'database'];

function walk(dir, callback) {
    fs.readdir(dir, function(err, list) {
        if (err) return callback(err);
        let pending = list.length;
        if (!pending) return callback(null);
        list.forEach(function(file) {
            file = path.resolve(dir, file);
            fs.stat(file, function(err, stat) {
                if (stat && stat.isDirectory()) {
                    walk(file, function(err, res) {
                        if (!--pending) callback(null);
                    });
                } else {
                    if (file.endsWith('.js') || file.endsWith('.vue') || file.endsWith('.php') || file.endsWith('.env')) {
                        let content = fs.readFileSync(file, 'utf8');
                        let newContent = content.replace(/MarionDev Store/g, 'NgodingPintar.id');
                        newContent = newContent.replace(/MarionDev/g, 'NgodingPintar');
                        newContent = newContent.replace(/mariondev\.site/g, 'ngodingpintar.id');
                        
                        if (content !== newContent) {
                            fs.writeFileSync(file, newContent, 'utf8');
                            console.log('Updated: ' + file);
                        }
                    }
                    if (!--pending) callback(null);
                }
            });
        });
    });
}

directoriesToSearch.forEach(d => {
    walk(path.join(dir, d), (err) => {
        if(err) console.error(err);
    });
});

// also process .env and .env.example
['.env', '.env.example'].forEach(f => {
    const file = path.join(dir, f);
    if(fs.existsSync(file)) {
        let content = fs.readFileSync(file, 'utf8');
        let newContent = content.replace(/MarionDev Store/g, 'NgodingPintar.id');
        newContent = newContent.replace(/APP_NAME="?MarionDev"?/g, 'APP_NAME="NgodingPintar.id"');
        newContent = newContent.replace(/MarionDev/g, 'NgodingPintar');
        if (content !== newContent) {
            fs.writeFileSync(file, newContent, 'utf8');
            console.log('Updated: ' + file);
        }
    }
});
