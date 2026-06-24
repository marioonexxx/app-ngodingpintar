const namedRoutes = {
    'admin.project-requests.destroy': '/admin/project-requests/:project',
};

const normalizeRouteParams = (params) => {
    if (params === undefined || params === null) {
        return {};
    }

    if (typeof params === 'object' && !Array.isArray(params)) {
        return params;
    }

    return { value: params };
};

const route = (name, params) => {
    let path = namedRoutes[name];

    if (!path) {
        throw new Error(`Route [${name}] is not defined in the frontend route map.`);
    }

    const routeParams = normalizeRouteParams(params);
    const positionalValue = routeParams.value;

    path = path.replace(/:([A-Za-z0-9_]+)/g, (match, key) => {
        const value = routeParams[key] ?? routeParams[key.replace(/[A-Z]/g, (letter) => `_${letter.toLowerCase()}`)] ?? positionalValue;

        if (value === undefined || value === null) {
            throw new Error(`Missing parameter [${key}] for route [${name}].`);
        }

        return encodeURIComponent(value);
    });

    return path;
};

try {
    console.log("Output string:");
    console.log(route('admin.project-requests.destroy', 1));
} catch (e) {
    console.error(e.message);
}
