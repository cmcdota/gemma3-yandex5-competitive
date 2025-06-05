from llama_cpp import Llama
import logging

# Настройка логирования
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)


def load_llm_model(repo_id: str, model_file: str = None, n_ctx: int = 2048, n_gpu_layers: int = -1):
    """
    Загрузка модели GGUF из HuggingFace Hub

    Args:
        repo_id: Идентификатор репозитория на HuggingFace (например "t-tech/T-pro-it-1.0-Q4_K_M-GGUF")
        model_file: Имя файла модели (если None, будет попытка автоматического определения)
        n_ctx: Размер контекста
        n_gpu_layers: Количество слоев для загрузки на GPU (-1 для всех)
    """
    from huggingface_hub import hf_hub_download

    # Скачиваем модель из HuggingFace Hub
    model_path = hf_hub_download(
        repo_id=repo_id,
        filename=model_file or "*Q4_K_M*.gguf",
        revision="main"
    )

    logger.info(f"Модель загружена по пути: {model_path}")

    # Инициализация LLM
    llm = Llama(
        model_path=model_path,
        n_ctx=n_ctx,
        n_gpu_layers=n_gpu_layers,
        verbose=False
    )

    return llm


def generate_text(llm: Llama, prompt: str, max_tokens: int = 256, temperature: float = 0.7):
    """
    Генерация текста с помощью модели

    Args:
        llm: Загруженная модель
        prompt: Текст промта
        max_tokens: Максимальное количество токенов для генерации
        temperature: Температура генерации (креативность)
    """
    output = llm(
        prompt,
        max_tokens=max_tokens,
        temperature=temperature,
        top_p=0.9,
        echo=False
    )

    return output['choices'][0]['text']


# Пример использования
if __name__ == "__main__":
    try:
        # Загружаем модель
        llm = load_llm_model(
            repo_id="t-tech/T-pro-it-1.0-Q4_K_M-GGUF",
            n_ctx=4096,
            n_gpu_layers=35  # Измените это значение в зависимости от вашей видеокарты
        )

        # Генерируем текст
        prompt = "Напиши код на Python для сортировки списка словарей по значению ключа 'age'"
        response = generate_text(llm, prompt)

        print("\nОтвет модели:")
        print(response)

    except Exception as e:
        logger.error(f"Произошла ошибка: {e}")