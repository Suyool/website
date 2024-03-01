import axios from "axios";

const useAxiosClient = () => {
  const axiosClient = axios.create({
    baseURL: "/touch",
    headers: {
      "Content-Type": "application/json",
    },
  });

  axiosClient.interceptors.response.use(
    (response) => {
      return response;
    },
    (error) => {
      // if (error.response && error.response.status === 401) {
      //   sessionStorage.clear();
      //   localStorage.clear();
      //   navigate("/");
      // }
      return Promise.reject(error);
    }
  );

  return axiosClient;
};

export default useAxiosClient;
