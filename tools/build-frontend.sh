#!/usr/bin/env bash
info() {
    echo "[INFO] $1"
}
error() {
    echo "[ERROR] $1"
    exit 1
}
copy_only=0
for arg in "$@"; do
    if [[ $arg == "--copy-only" ]]; then
        copy_only=1
        break
    fi
done
rm -rf build/
mkdir build
info "starting building..."
cd ../frontend/
if [[ $copy_only -eq 0 ]]; then
    if [[ ! -e yarn.lock ]]; then
        error "yarn.lock is not found."
    fi
    if [[ ! -e node_modules || ! -d node_modules ]]; then
        info "node_modules is not found. running \"yarn install\"..."
        yarn install
        [[ $? -ne 0 ]] && error "failed to run \"yarn install\"."
    fi
    info "running \"yarn build\"..."
    yarn build
    [[ $? -ne 0 ]] && error "failed to run \"yarn build\"."
else
    if [[ ! -e build || ! -d build ]]; then
        error "no source to copy."
    fi
fi
info "copying files..."
for file in ./build/*; do
    if [[ -d $file ]]; then
        cp -r $file ../tools/build/
        continue
    fi
    if [[ $file != "./build/service-worker.js" && ! $file =~ -manifest\. ]]; then
        cp $file ../tools/build/
    fi
done
info "processing files..."
cd ../tools/build/
mv index.html control-panel.php
cd ..
php index-decorator.php
[[ $? -ne 0 ]] && error "failed to run \"php index-decorator.php\"."
info "done."
