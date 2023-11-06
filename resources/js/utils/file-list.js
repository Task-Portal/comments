import { ref } from "vue";

export default function () {
    const files = ref([]);


    async function addFiles(newFiles) {
        console.log("NewFiles: ", newFiles);
        const newUploadableFiles = await Promise.all([...newFiles]
            .filter((file) => checkFileExtensions(file))
            .map(async (file) => {
                if (file.type.startsWith("image/")) {
                    try {
                        const resizedBlob = await resizeImage(file, 320, 240);
                        return new UploadableFile(resizedBlob);
                    } catch (error) {
                        console.error("Error resizing image: ", error);
                        return null;
                    }
                } else {
                    return new UploadableFile(file);
                }
            }));

        files.value = files.value
            .concat(newUploadableFiles.filter((file) => !fileExists(file.id)))
            .slice(0, 3);

    }


    function fileExists(otherId) {
        return files.value.some(({ id }) => id === otherId);
    }

    function checkFileExtensions(file) {
        if (file.name.match(/([^\s]+(?=\.(txt))\.\2)/i) && file.size < 102400) {
            return true;
        }

        return file.name.match(/([^\s]+(?=\.(jpg|gif|png|jpeg))\.\2)/i)
            ? true
            : false;
    }

    async function resizeImage(file, maxWidth, maxHeight) {
        return new Promise((resolve, reject) => {
            const image = new Image();
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");

            image.onload = function () {
                let width = image.width;
                let height = image.height;

                if (width > maxWidth || height > maxHeight) {
                    if (width > maxWidth) {
                        const ratio = maxWidth / width;
                        width = maxWidth;
                        height *= ratio;
                    }

                    if (height > maxHeight) {
                        const ratio = maxHeight / height;
                        height = maxHeight;
                        width *= ratio;
                    }

                    canvas.width = width;
                    canvas.height = height;
                    ctx.drawImage(image, 0, 0, width, height);

                    canvas.toBlob((blob) => {
                        const resizedFile = new File([blob], file.name, {
                            type: file.type,
                        });
                        resolve(resizedFile);
                    }, file.type);
                } else {
                    resolve(file);
                }
            };

            image.onerror = function () {
                reject(new Error("Failed to load the image."));
            };

            image.src = URL.createObjectURL(file);

        });
    }

    function removeFile(file) {
        const index = files.value.indexOf(file);

        if (index > -1) files.value.splice(index, 1);

    }

    return { files, addFiles, removeFile };
}

class UploadableFile {
    constructor(file) {
        this.file = file;
        this.id = `${file.name}-${file.size}-${file.lastModified}-${file.type}`;
        this.url = URL.createObjectURL(file);
        this.status = null;
    }
}
