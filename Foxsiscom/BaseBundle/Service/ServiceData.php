<?php
namespace Foxsiscom\BaseBundle\Service;

use Foxsiscom\BaseBundle\Service\Exception\ServiceDataException;

class ServiceData implements \ArrayAccess
{

    /**
     *
     * @var mixed[]
     */
    protected $data = null;

    private function __construct()
    {}

    /**
     * carrega no objeto o array passado
     *
     * @param array $data
     */
    public function load(array $data)
    {
        $this->data = $data;
    }

    /**
     * fabrica um objeto, podendo opcionalmente carregar os dados
     *
     * @param array $data
     * @return ServiceData
     */
    static public function build(array $data = null)
    {
        $sd = new self();
        if ($data !== null) {
            $sd->load($data);
        }
        return $sd;
    }

    /**
     * alias para o metodo offsetSet()
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function set($offset, $value)
    {
        $this->offsetSet($offset, $value);
        return $this;
    }

    /**
     * alias para o metodo offsetGet().
     * Se nao for passado o $offset, retorna o array inteiro.
     *
     * @param mixed $offset
     * @return mixed
     */
    public function get($offset = null)
    {
        if ($offset === null) {
            if (empty($this->data)) {
                throw new ServiceDataException('O objeto ainda nao foi carregado.');
            }
            return $this->data;
        }
        return $this->offsetGet($offset);
    }

    /**
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
        return $this;
    }

    /**
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->data) && array_key_exists($offset, $this->data);
    }

    /**
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     *
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->data[$offset];
        } else {
            throw new \InvalidArgumentException($offset . ' nao existe.');
        }
    }

    /**
     * alias para set('user', $user)
     *
     * @param
     *            User
     */
    public function setUser($user)
    {
        $this->set('user', $user);
        return $this;
    }

    /**
     * alias para get('user')
     *
     * @param
     *            User
     */
    public function getUser()
    {
        return $this->get('user');
    }

    /**
     * alias para o metodo offsetSet()
     *
     * @param
     *            mixed
     * @return ServiceData
     */
    public function setFile($file)
    {
        $this->set('file', $file);
        return $this;
    }

    /**
     * alias para get('file')
     *
     * @return mixed
     */
    public function getFile()
    {
        return $this->get('file');
    }

    /**
     *
     * @param mixed $offset
     */
    public function has($offset)
    {
        return isset($this->data[$offset]);
    }
}