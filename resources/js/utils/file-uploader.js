import { router } from "@inertiajs/vue3";

export async function uploadFile(file, url, number) {
    // set up the request data
    let formData = new FormData()
    formData.append('file', file.file)

    // track status and upload file
    file.status = 'loading'
    console.log("Url: ", url)
    let response = await fetch(url, { method: 'POST', body: formData })

    // change status to indicate the success of the upload request
    file.status = response.ok

    return response


}

export function uploadFiles(files, url, number) {
    return Promise.all(files.map((file) => uploadFile(file, url,number)));
}

export default function createUploader(url, number) {
    return {
        uploadFile: function (file) {
            return uploadFile(file, url, number);
        },
        uploadFiles: function (files) {
            return uploadFiles(files, url, number);
        },
    };
}
