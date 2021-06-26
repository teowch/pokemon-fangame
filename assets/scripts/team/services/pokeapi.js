async function request(category, id) {
    let response;

    await $.getJSON({
        url: `https://pokeapi.co/api/v2/${category}/${id}`,
        success: data => response = data,
        error: () => response = null
    });

    return response;
}

export default request;