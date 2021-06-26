async function request(path) {
    let response;

    await $.getJSON({
        url: `https://localhost/TESTS/storage/${path}`,
        success: data => response = data,
        error: () => response = null
    });

    return response;
}

export default request;