import { useEffect, useState } from 'react';
import axios from 'axios';

/**
 * useFetch with polling (auto refresh)
 * @param {boolean} shouldFetch - apakah fetch diaktifkan
 * @param {string|null} url - URL endpoint (Laravel API)
 * @param {object} config - axios config
 * @param {number} interval - waktu polling dalam ms (default: 5000)
 */
export default function useFetch(shouldFetch, url, config = {}, interval = 5000) {
    const [data, setData] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    useEffect(() => {
        if (!shouldFetch || !url) {
            setData([]);
            setError(null);
            return;
        }

        let cancelled = false;
        let timer = null;

        const fetchData = () => {
            setLoading(true);
            setError(null);

            axios
                .get(url, config)
                .then((res) => {
                    if (!cancelled) {
                        setData(res.data);
                    }
                })
                .catch((err) => {
                    if (!cancelled) {
                        setError(err);
                    }
                })
                .finally(() => {
                    if (!cancelled) {
                        setLoading(false);
                    }
                });
        };

        // Fetch pertama
        fetchData();

        // Auto refresh setiap X detik
        timer = setInterval(() => {
            if (!cancelled) fetchData();
        }, interval);

        return () => {
            cancelled = true;
            clearInterval(timer);
        };
    }, [shouldFetch, url, interval]);

    return { data, loading, error };
}
